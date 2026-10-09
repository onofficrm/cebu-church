<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if ($is_guest) {
    alert('다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.', G5_BBS_URL.'/login.php?wr_id='.$wr_id.'&amp;'.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id)));
    exit;
}

$my_p = isset($member['mb_point']) ? $member['mb_point'] : '0';
$file_p = isset($write['wr_point']) ? $write['wr_point'] : '0';

if ($is_admin || $file_p < 1) { // 관리자이거나 게시물 포인트가 0인경우 다운로드 가능
    send_download_headers($filepath, $original);
    
    $fp = fopen($filepath, 'rb');
    if (!$fp) {
        alert('파일을 열 수 없습니다.');
        exit;
    }
    
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        fpassthru($fp);
    } finally {
        fclose($fp);
    }
    flush();

    //log_download($member['mb_id'], $file['bf_source'], $wr_id);
    
} elseif ($file_p > 0 && $my_p >= $file_p) { //포인트가 충분한 경우
    send_download_headers($filepath, $original);
    
    $fp = fopen($filepath, 'rb');
    if (!$fp) {
        alert('파일을 열 수 없습니다.');
        exit;
    }
    
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        fpassthru($fp);
    } finally {
        fclose($fp);
    }
    flush();

    // 포인트 차감
    if ($write['mb_id'] && $write['mb_id'] == $member['mb_id']) {
        // 게시물 작성자 본인은 포인트 차감 없음
        ;
    } else {
        

        // 게시물당 한번만 차감하도록
        insert_point($member['mb_id'], (int)$file_p * (-1), "{$board['bo_subject']} $wr_id 파일 구매", $bo_table, $wr_id, "다운로드");
        
        //수수료설정 "원" 단위가 우선적용
        if(isset($board['bo_2']) && $board['bo_2'] > 0) {
            
            $ssr = $board['bo_2'];

            $reduced_amount_2 = $file_p - $ssr;
            $file_p = $reduced_amount_2;
            
        } else if(isset($board['bo_1']) && $board['bo_1'] > 0) {
            
            $ssr = $board['bo_1'];

            $discount_amount = $file_p * ($ssr / 100);
            $reduced_amount = $file_p - $discount_amount;
            
            $file_p = $reduced_amount;
            
        }
        
        // 차감 후 정산처리
        insert_point($write['mb_id'], (int)$file_p, "{$board['bo_subject']} $wr_id 파일 판매", $bo_table, $wr_id, $member['mb_id'].'-'.uniqid(''));
        memo_auto_send($board['bo_subject'].'에 등록한 파일이 판매 되었습니다.', G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&wr_id='.$wr_id, $write['mb_id'], "system-msg");
    }

    //log_download($member['mb_id'], $file['bf_source'], $wr_id);
        
} elseif ($my_p < $file_p) { // 포인트가 부족한 경우
    alert('보유하신 포인트가 부족합니다.');
    exit;
} else {
    alert('다운로드 권한이 없습니다.');
    exit;
}

function send_download_headers($filepath, $original) {
    header("Content-Type: application/zip");
    header("Content-Length: " . filesize($filepath));
    header("Content-Disposition: attachment; filename=\"$original\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    flush();
}

function log_download($member_id, $file_name, $post_id, $success = true) {
    $log_file = G5_DATA_PATH . "/download_logs/" . date("Ymd") . ".log";
    $status = $success ? "downloaded" : "failed to download";
    $log_message = "[" . date("Y-m-d H:i:s") . "] Member ID: $member_id $status file: $file_name from post ID: $post_id\n";
    error_log($log_message, 3, $log_file);
}
?>
