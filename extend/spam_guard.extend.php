<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 게시판 스팸 방어.
 * 글쓰기 폼에서만 발급하는 값을 확인하고, 가입 직후·연속 등록·유흥 스팸 문구를 막습니다.
 */

define('SPAM_GUARD_MIN_SECONDS', 3);
define('SPAM_GUARD_MAX_SECONDS', 604800);
define('SPAM_GUARD_POST_LIMIT', 12);
define('SPAM_GUARD_POST_WINDOW', 3600);
define('SPAM_GUARD_JOIN_LIMIT', 3);
define('SPAM_GUARD_JOIN_WINDOW', 86400);
define('SPAM_GUARD_NEW_MEMBER_WAIT', 3600);

add_event('write_update_before', 'spam_guard_on_write', G5_HOOK_DEFAULT_PRIORITY, 4);
add_event('comment_update_after', 'spam_guard_on_comment_after', G5_HOOK_DEFAULT_PRIORITY, 7);
add_event('register_form_update_before', 'spam_guard_on_register', G5_HOOK_DEFAULT_PRIORITY, 2);

function spam_guard_boot()
{
    $self = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    if (!preg_match('#/(write|board|register_form)\.php$#', $self)) {
        return;
    }
    ob_start('spam_guard_inject');
}

function spam_guard_fields()
{
    $ts = (int) G5_SERVER_TIME;
    return '<div style="position:absolute;left:-9999px;height:0;overflow:hidden" aria-hidden="true">'
        . '<label>회사 <input type="text" name="sg_company" value="" tabindex="-1" autocomplete="off"></label>'
        . '</div>'
        . '<input type="hidden" name="sg_ts" value="' . $ts . '">';
}

function spam_guard_inject($html)
{
    if (!is_string($html) || $html === '') {
        return $html;
    }
    $fields = spam_guard_fields();
    $injected = preg_replace_callback(
        '/(<form\b[^>]*\bname=["\'](?:fwrite|fviewcomment|fregisterform)["\'][^>]*>)/i',
        function ($matches) use ($fields) {
            return $matches[1] . $fields;
        },
        $html
    );
    return is_string($injected) ? $injected : $html;
}

function spam_guard_has_phone($text)
{
    $compact = preg_replace('/[\s\-\._·ㆍㅡ=~↔●◆\[\]\(\)<>\/]+/u', '', $text);
    if (!is_string($compact) || $compact === '') {
        return false;
    }
    $normalized = strtr($compact, array(
        'O' => '0', 'o' => '0', 'I' => '1', 'l' => '1', 'ㅣ' => '1',
    ));
    return preg_match('/01[016789][0-9]{7,8}/', $normalized) === 1;
}

function spam_guard_text_is_spam($text)
{
    if (!is_string($text) || $text === '') {
        return false;
    }
    if (stripos($text, 'nightlife-article') !== false || strpos($text, '등록 확인 코드') !== false) {
        return true;
    }
    if (preg_match('/수집확인\s*\d+/u', $text) === 1) {
        return true;
    }
    $strong = '/쓰리노|룸싸롱|풀싸롱|풀싸|호빠|호스트바|호스트빠|출장마사지|출장안마|출장샵|콜걸|셔츠룸|가라오케|텐프로|하이퍼블릭|기모노|레깅스룸|유흥주점|룸빵|미러룸|노래방|노래빠|노래클럽|노래주점|퍼블릭/u';
    return preg_match($strong, $text) === 1;
}

function spam_guard_form_error()
{
    if (!isset($_POST['sg_ts']) || !isset($_POST['sg_company'])) {
        return '글쓰기 화면을 새로 연 뒤 다시 등록해 주세요.';
    }
    if (trim((string) $_POST['sg_company']) !== '') {
        return '올바른 방법으로 이용해 주십시오.';
    }
    $age = G5_SERVER_TIME - (int) $_POST['sg_ts'];
    if ($age < SPAM_GUARD_MIN_SECONDS || $age > SPAM_GUARD_MAX_SECONDS) {
        return '글쓰기 화면을 연 뒤 잠시 후 다시 등록해 주세요.';
    }
    return '';
}

function spam_guard_track($bucket, $limit, $window)
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    if ($ip === '') {
        return false;
    }
    $dir = G5_DATA_PATH . '/spam_guard';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
        @file_put_contents($dir . '/.htaccess', "Require all denied\nDeny from all\n");
    }
    $path = $dir . '/' . $bucket . '.json';
    $handle = @fopen($path, 'c+');
    if (!$handle) {
        return false;
    }
    flock($handle, LOCK_EX);
    $raw = stream_get_contents($handle);
    $data = json_decode($raw ? $raw : '', true);
    if (!is_array($data)) {
        $data = array();
    }
    $now = time();
    $key = md5($ip);
    $times = (isset($data[$key]) && is_array($data[$key])) ? $data[$key] : array();
    $fresh = array();
    foreach ($times as $time) {
        if (($now - (int) $time) < $window) {
            $fresh[] = (int) $time;
        }
    }
    $blocked = count($fresh) >= $limit;
    if (!$blocked) {
        $fresh[] = $now;
        $data[$key] = $fresh;
        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, json_encode($data));
        fflush($handle);
    }
    flock($handle, LOCK_UN);
    fclose($handle);
    return $blocked;
}

function spam_guard_member_too_new()
{
    global $is_member, $is_admin, $member;
    if ($is_admin === 'super' || !$is_member || empty($member['mb_datetime'])) {
        return false;
    }
    $joined = strtotime($member['mb_datetime']);
    if (!$joined) {
        return false;
    }
    return (G5_SERVER_TIME - $joined) < SPAM_GUARD_NEW_MEMBER_WAIT;
}

function spam_guard_assert($subject, $content, $mode)
{
    global $is_admin, $w;

    if ($is_admin === 'super') {
        return;
    }

    $form_error = spam_guard_form_error();
    if ($form_error !== '') {
        alert($form_error);
    }

    $creating = ($mode === 'register') || ($mode === 'write' && $w !== 'u') || ($mode === 'comment' && $w !== 'cu');
    if ($creating && $mode !== 'register' && spam_guard_member_too_new()) {
        alert('가입 후 1시간이 지나야 글을 등록할 수 있습니다.');
    }
    if ($creating && $mode === 'register' && spam_guard_track('join', SPAM_GUARD_JOIN_LIMIT, SPAM_GUARD_JOIN_WINDOW)) {
        alert('짧은 시간에 가입을 너무 많이 시도했습니다.');
    }
    if ($creating && $mode !== 'register' && spam_guard_track('post', SPAM_GUARD_POST_LIMIT, SPAM_GUARD_POST_WINDOW)) {
        alert('짧은 시간에 너무 많은 글을 등록할 수 없습니다.');
    }

    $text = trim($subject . "\n" . html_entity_decode(strip_tags($content), ENT_QUOTES, 'UTF-8'));
    if (spam_guard_text_is_spam($text) || ($mode !== 'register' && spam_guard_has_phone($text) && preg_match('/알바|출장|유흥|예약/u', $text))) {
        alert('스팸으로 판단되어 등록이 차단되었습니다.');
    }
}

function spam_guard_on_write($board, $wr_id, $w, $qstr)
{
    global $wr_subject, $wr_content;
    spam_guard_assert((string) $wr_subject, (string) $wr_content, 'write');
}

function spam_guard_on_register($mb_id, $w)
{
    global $mb_name, $mb_nick;
    if ($w !== '') {
        $form_error = spam_guard_form_error();
        if ($form_error !== '') {
            alert($form_error);
        }
        return;
    }
    $name = trim((string) $mb_name . ' ' . (string) $mb_nick . ' ' . (string) $mb_id);
    spam_guard_assert($name, '', 'register');
}

function spam_guard_on_comment_after($board, $wr_id, $w, $qstr, $redirect_url, $comment_id, $reply_array)
{
    global $is_admin, $bo_table, $g5, $write_table;

    if ($is_admin === 'super' || !$comment_id) {
        return;
    }
    $comment = get_write($write_table, $comment_id);
    if (empty($comment['wr_id'])) {
        return;
    }
    $text = html_entity_decode(strip_tags($comment['wr_content']), ENT_QUOTES, 'UTF-8');
    if (!spam_guard_text_is_spam($text)) {
        return;
    }

    sql_query(" delete from {$write_table} where wr_id = '" . (int) $comment_id . "' ");
    sql_query(" update {$write_table} set wr_comment = if(wr_comment > 0, wr_comment - 1, 0) where wr_id = '" . (int) $comment['wr_parent'] . "' ");
    sql_query(" update {$g5['board_table']} set bo_count_comment = if(bo_count_comment > 0, bo_count_comment - 1, 0) where bo_table = '{$bo_table}' ");
    sql_query(" delete from {$g5['board_new_table']} where bo_table = '{$bo_table}' and wr_id = '" . (int) $comment_id . "' ");
    delete_cache_latest($bo_table);
    alert('스팸으로 판단되어 등록이 차단되었습니다.');
}

spam_guard_boot();
