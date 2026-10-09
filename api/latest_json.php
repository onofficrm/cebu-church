<?php
include_once('./_common.php'); // 그누보드 설정 로드
include_once(G5_LIB_PATH.'/thumbnail.lib.php'); // 썸네일 라이브러리 로드

// CORS 허용 (리액트 앱이 다른 도메인이나 포트에서 접속할 때 필요)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

$bo_table = $_GET['bo_table'];
$rows = isset($_GET['rows']) ? (int)$_GET['rows'] : 4;

if (!$bo_table) {
    echo json_encode(array("error" => "bo_table is required"));
    exit;
}

// 게시판 테이블명 확인
$tmp_write_table = $g5['write_prefix'] . $bo_table;

// 최신글 쿼리
$sql = " select * from {$tmp_write_table} where wr_is_comment = 0 order by wr_num limit 0, {$rows} ";
$result = sql_query($sql);

$list = array();
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // 썸네일 생성 및 가져오기 (가로 500, 세로 350)
    $thumb = get_list_thumbnail($bo_table, $row['wr_id'], 500, 350, false, true);
    $img_url = $thumb['src'] ? $thumb['src'] : 'https://images.unsplash.com/photo-1438232992991-995b7058bbb3?w=500&q=80';

    $list[$i]['id'] = $row['wr_id'];
    $list[$i]['title'] = $row['wr_subject'];
    $list[$i]['content'] = cut_str(strip_tags($row['wr_content']), 100);
    $list[$i]['date'] = date("Y.m.d", strtotime($row['wr_datetime']));
    $list[$i]['img'] = $img_url;
    $list[$i]['link'] = G5_BBS_URL."/board.php?bo_table=".$bo_table."&wr_id=".$row['wr_id'];
    
    // 유튜브 게시판의 경우 wr_1 등에 저장된 영상 ID나 링크를 활용할 수 있습니다.
    $list[$i]['video_id'] = $row['wr_1']; 
}

echo json_encode($list);
?>