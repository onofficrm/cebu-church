<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// Tailwind CSS 로드 (디자인 보정용)
add_stylesheet('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<style>
/* 모바일 및 PC 공통 스타일 보정 */
#custom_view_wrap { overflow-x: hidden !important; width: 100% !important; box-sizing: border-box !important; }

/* 유튜브 반응형 플레이어 */
.video-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 비율 */
    height: 0;
    overflow: hidden;
    max-width: 100%;
    background: #000;
    margin-bottom: 15px;
    border-radius: 8px;
}
.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

/* 이미지 리사이징 */
#bo_v_atc img { max-width: 100% !important; height: auto !important; display: block !important; margin: 10px auto !important; }

/* 모바일 전용 스타일 강제 오버라이드 */
@media (max-width: 768px) {
    #custom_view_wrap { padding: 15px !important; }
    .rb_bbs_wrap { width: 100% !important; }
    
    /* 제목 및 정보 정렬 */
    h2 { font-size: 1.25rem !important; font-weight: 800 !important; line-height: 1.4 !important; margin-bottom: 10px !important; word-break: break-all !important; }
    .rb_bbs_for_mem { display: flex !important; flex-wrap: wrap !important; gap: 10px !important; padding: 10px 0 !important; border-bottom: 1px solid #eee !important; }
    .rb_bbs_for_mem li { list-style: none !important; font-size: 12px !important; }

    /* 버튼 겹침 해결 */
    .btm_btns_right { display: flex !important; flex-wrap: wrap !important; gap: 8px !important; float: none !important; width: 100% !important; margin-top: 20px !important; }
    .fl_btns { flex: 1 !important; text-align: center !important; padding: 10px !important; border-radius: 8px !important; min-width: 80px !important; display: flex !important; align-items: center !important; justify-content: center !important; background: #f8fafc !important; border: 1px solid #e2e8f0 !important; }
    .main_color_bg { background: #4f46e5 !important; color: #fff !important; }
    
    /* 이전/다음글 정렬 */
    .bo_v_nb { margin-top: 20px !important; border-top: 1px solid #eee !important; }
    .bo_v_nb li { padding: 12px 0 !important; border-bottom: 1px solid #eee !important; cursor: pointer !important; }
    .nb_tit { display: inline-block !important; width: 60px !important; font-weight: bold !important; color: #4f46e5 !important; }

    /* 댓글창 강제 교정 */
    #bo_vc_w { width: 100% !important; margin: 15px 0 !important; }
    #bo_vc_w textarea { width: 100% !important; border: 1px solid #ddd !important; border-radius: 8px !important; padding: 10px !important; }
    .bo_vc_w_info { display: flex !important; flex-direction: column !important; gap: 8px !important; margin-bottom: 10px !important; }
    .bo_vc_w_info input { width: 100% !important; height: 40px !important; border: 1px solid #ddd !important; border-radius: 6px !important; }
}
</style>

<div id="custom_view_wrap" class="max-w-5xl mx-auto">
    <div class="rb_bbs_wrap">
        
        <div class="mt-4 mb-2">
            <span class="text-slate-400 text-xs"><?php echo date("Y.m.d H:i", strtotime($view['wr_datetime'])) ?></span>
            <h2 class="mt-2"><?php echo get_text($view['wr_subject']);?></h2>
        </div>

        <ul class="rb_bbs_for_mem mb-6">
            <li><b><?php echo $view['name'] ?></b></li>
            <li class="text-slate-400">조회 <?php echo number_format($view['wr_hit']); ?></li>
            <li class="text-slate-400">댓글 <?php echo number_format($view['wr_comment']); ?></li>
        </ul>

        <div id="bo_v_atc">
            <?php if(isset($view['wr_1']) && $view['wr_1']) { 
                // 유튜브 ID 추출 함수
                function getYouTubeVideoId($input) {
                    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) return $input;
                    preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/|.+\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $input, $matches);
                    return $matches[1] ?? null;
                }
                $videoId = getYouTubeVideoId($view['wr_1']);
            ?>
                <div class="video-container">
                    <iframe src="https://www.youtube.com/embed/<?php echo $videoId; ?>" allowfullscreen></iframe>
                </div>
                
                <div class="mb-6 p-3 bg-slate-50 rounded text-sm break-all">
                    <a href="<?php echo $view['wr_1']; ?>" target="_blank" class="text-blue-600 font-bold">
                        <i class="fa fa-link"></i> <?php echo $view['wr_1']; ?>
                    </a>
                </div>
            <?php } ?>

            <?php if(count($view['file'])) {
                echo "<div id=\"bo_v_img\" class=\"mb-6\">";
                foreach($view['file'] as $view_file) {
                    echo get_file_thumbnail($view_file);
                }
                echo "</div>";
            } ?>

            <div class="prose prose-slate max-w-none break-all">
                <?php echo get_view_thumbnail($view['content']); ?>
            </div>
        </div>

        <div class="btm_btns_right">
            <?php if ($list_href) { ?><a href="<?php echo $list_href ?>" class="fl_btns">목록</a><?php } ?>
            <?php if ($update_href) { ?><a href="<?php echo $update_href ?>" class="fl_btns">수정</a><?php } ?>
            <?php if ($delete_href) { ?><a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;" class="fl_btns text-red-500">삭제</a><?php } ?>
            <?php if ($write_href) { ?>
            <a href="<?php echo $write_href ?>" class="fl_btns main_color_bg">글쓰기</a>
            <?php } ?>
        </div>

        <div class="bo_v_nb">
            <?php if ($prev_href) { ?>
                <li onclick="location.href='<?php echo $prev_href ?>';">
                    <span class="nb_tit">이전글</span> <?php echo $prev_wr_subject;?>
                </li>
            <?php } ?>
            <?php if ($next_href) { ?>
                <li onclick="location.href='<?php echo $next_href ?>';">
                    <span class="nb_tit">다음글</span> <?php echo $next_wr_subject;?>
                </li>
            <?php } ?>
        </div>

        <div class="mt-10">
            <?php include_once(G5_BBS_PATH.'/view_comment.php'); ?>
        </div>
    </div>
</div>

<script>
$(function() {
    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();
});
</script>