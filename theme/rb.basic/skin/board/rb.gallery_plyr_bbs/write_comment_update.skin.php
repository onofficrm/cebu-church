<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if($w == "c") { // 신규댓글 이면,
    
    //댓글의 댓글 작성시 쪽지
    if (strlen($tmp_comment_reply) > 0) {
        //원글 작성자가 코멘트 입력이나 수정시 패스
        if ($reply_array['mb_id'] == $mb_id || !$reply_array['mb_id']) {
            // return 0;
        } else {
            if ($is_member){
                $smember_id = $mb_id;
            } else {
                //손님에게 코멘트 허용시 또는 테스트용 아이디
                $smember_id = "guest";
            }

            $link_url = G5_BBS_URL."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id."&".$qstr."#c_{$comment_id}";
            memo_auto_send($board['bo_subject'].'의 게시물의 댓글에 댓글이 등록 되었습니다.', $link_url, $reply_array['mb_id'], "system-msg");
        }
    }


    //게시물의 댓글 작성시 작성자에게 쪽지
    //원글 작성자가 코멘트 입력이나 수정시 또는 코멘트답변 입력시 패스
    if ($wr['mb_id'] == $member['mb_id'] || $wr['mb_id'] == $reply_array['mb_id']) {
        // return 0;
    } else {
        if ($is_member){
            $smember_id = $member['mb_id'];
        } else {
            //손님에게 코멘트 허용시 또는 테스트용 아이디
            $smember_id = "guest";
        }


        $link_url = G5_BBS_URL."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id."&".$qstr."#c_{$comment_id}";
        memo_auto_send($board['bo_subject'].'의 게시물에 댓글이 등록 되었습니다.', $link_url, $wr['mb_id'], "system-msg");
    }

}



// 웹앱 관련
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == $app['ap_title']) {
    if($w == "c") { 
        alert('댓글이 등록 되었습니다.');
    } else {
        alert('댓글이 수정 되었습니다.');
    }
}

?> 
