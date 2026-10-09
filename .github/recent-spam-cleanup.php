<?php
/**
 * One-time cleanup for spam showing on the recent-posts page.
 * Uploaded with a random token, then removed.
 */
$cleanup_token = '__TOKEN__';
$cleanup_given = isset($_GET['k']) ? (string) $_GET['k'] : '';
$cleanup_op = isset($_GET['op']) ? (string) $_GET['op'] : 'preview';

if (!hash_equals($cleanup_token, $cleanup_given)) {
    http_response_code(404);
    exit;
}
if ($cleanup_op !== 'preview' && $cleanup_op !== 'delete') {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('ok' => false, 'error' => 'bad op'));
    exit;
}

include_once __DIR__ . '/common.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex');

function cleanup_fail($error, $extra = array())
{
    http_response_code(409);
    echo json_encode(array_merge(array('ok' => false, 'error' => $error), $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

function cleanup_is_protected($subject)
{
    return preg_match('/주보|예배|설교|헌금|세례|집회|찬양|교회|성탄|구제|바자|선교회|할렐루야|코스타|축제|구역|세미나|광고/u', $subject) === 1;
}

function cleanup_is_spam($subject)
{
    if (cleanup_is_protected($subject)) {
        return false;
    }
    if (preg_match('/^수집확인/u', $subject) === 1) {
        return true;
    }
    return preg_match('/알바|쓰리노|룸싸롱|가라오케|출장|홈타이|셔츠룸|퍼블릭|호빠|호스트|콜걸|노래방|노래빠|노래클럽|노래주점|하이퍼|하퍼|풀싸|풀싸롱|쩜오|기모노|유흥|안마|마사지|레깅스|터치룸|란제리|텐프로|아가씨|미러룸|룸빵|다국적|구장|가요장|3\s*no/ui', $subject) === 1;
}

function cleanup_editor_files($contents)
{
    $matches = get_editor_image($contents, false);
    if (!$matches || empty($matches[1])) {
        return;
    }
    $root = realpath(G5_DATA_PATH);
    if (!$root) {
        return;
    }
    $root = rtrim(str_replace('\\', '/', $root), '/') . '/';
    foreach ($matches[1] as $src) {
        $imgurl = @parse_url($src);
        if (empty($imgurl['path'])) {
            continue;
        }
        $path = G5_PATH . $imgurl['path'];
        $real = realpath($path);
        if (!$real || !is_file($real)) {
            continue;
        }
        $real_norm = str_replace('\\', '/', $real);
        if (strpos($real_norm, $root) !== 0) {
            continue;
        }
        @unlink($real);
    }
}

$boards = array('gallery', 'youtube');
$targets = array();
foreach ($boards as $bo_table) {
    $board = get_board_db($bo_table, true);
    if (empty($board['bo_table'])) {
        cleanup_fail('board missing', array('bo_table' => $bo_table));
    }
    $write_table = $g5['write_prefix'] . $bo_table;
    $result = sql_query(" select * from {$write_table} where wr_is_comment = 0 order by wr_id desc ");
    while ($row = sql_fetch_array($result)) {
        if (!cleanup_is_spam($row['wr_subject'])) {
            continue;
        }
        $row['_bo_table'] = $bo_table;
        $row['_board'] = $board;
        $row['_write_table'] = $write_table;
        $targets[] = $row;
    }
}

if (!$targets) {
    cleanup_fail('nothing to delete');
}
if (count($targets) > 30) {
    cleanup_fail('too many matches', array('selected' => count($targets)));
}

$subjects = array();
foreach ($targets as $row) {
    $subjects[] = $row['_bo_table'] . ': ' . $row['wr_subject'];
}

if ($cleanup_op === 'preview') {
    echo json_encode(array(
        'ok' => true,
        'mode' => 'preview',
        'selected' => count($targets),
        'subjects' => $subjects,
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

$count_write = 0;
$count_comment = 0;
$touched = array();
foreach ($targets as $write) {
    $bo_table = $write['_bo_table'];
    $board = $write['_board'];
    $write_table = $write['_write_table'];
    $wr_id = (int) $write['wr_id'];
    $children = sql_query(" select wr_id, mb_id, wr_is_comment, wr_content from {$write_table} where wr_parent = '{$wr_id}' order by wr_id ");
    while ($row = sql_fetch_array($children)) {
        if (!$row['wr_is_comment']) {
            if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], '쓰기')) {
                insert_point($row['mb_id'], $board['bo_write_point'] * (-1), "{$board['bo_subject']} {$row['wr_id']} 글삭제");
            }
            $files = sql_query(" select * from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$row['wr_id']}' ");
            while ($file = sql_fetch_array($files)) {
                $delete_file = run_replace('delete_file_path', G5_DATA_PATH . '/file/' . $bo_table . '/' . str_replace('../', '', $file['bf_file']), $file);
                if (is_file($delete_file)) {
                    @unlink($delete_file);
                }
                if (preg_match("/\.({$config['cf_image_extension']})$/i", $file['bf_file'])) {
                    delete_board_thumbnail($bo_table, $file['bf_file']);
                }
            }
            cleanup_editor_files($row['wr_content']);
            delete_editor_thumbnail($row['wr_content']);
            sql_query(" delete from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$row['wr_id']}' ");
            $count_write++;
        } else {
            if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], '댓글')) {
                insert_point($row['mb_id'], $board['bo_comment_point'] * (-1), "{$board['bo_subject']} {$wr_id}-{$row['wr_id']} 댓글삭제");
            }
            $count_comment++;
        }
    }
    sql_query(" delete from {$write_table} where wr_parent = '{$wr_id}' ");
    sql_query(" delete from {$g5['board_new_table']} where bo_table = '{$bo_table}' and wr_parent = '{$wr_id}' ");
    sql_query(" delete from {$g5['scrap_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' ");
    $bo_notice = board_notice($board['bo_notice'], $wr_id);
    sql_query(" update {$g5['board_table']} set bo_notice = '" . sql_real_escape_string($bo_notice) . "' where bo_table = '{$bo_table}' ");
    $board['bo_notice'] = $bo_notice;
    $touched[$bo_table] = $write_table;
}

foreach ($touched as $bo_table => $write_table) {
    $write_count = sql_fetch(" select count(*) as cnt from {$write_table} where wr_is_comment = 0 ");
    $comment_count = sql_fetch(" select count(*) as cnt from {$write_table} where wr_is_comment = 1 ");
    sql_query(" update {$g5['board_table']} set bo_count_write = '{$write_count['cnt']}', bo_count_comment = '{$comment_count['cnt']}' where bo_table = '{$bo_table}' ");
    delete_cache_latest($bo_table);
}

echo json_encode(array(
    'ok' => true,
    'mode' => 'delete',
    'deleted_posts' => $count_write,
    'deleted_comments' => $count_comment,
    'subjects' => $subjects,
), JSON_UNESCAPED_UNICODE);
