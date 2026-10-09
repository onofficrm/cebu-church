<?php
if (!defined('_GNUBOARD_')) exit; 
add_stylesheet('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">', 0);
?>

<div id="custom_view_wrap" class="max-w-5xl mx-auto px-4 py-6 md:py-16 font-sans text-slate-900">
    <div class="mb-8">
        <h2 class="text-xl md:text-4xl font-black text-slate-900 break-all mb-4">
            <?php echo get_text($view['wr_subject']); ?>
        </h2>
        <div class="flex justify-between pb-4 border-b border-slate-100 text-xs text-slate-400">
            <span><?php echo $view['name'] ?></span>
            <span><?php echo date("Y.m.d", strtotime($view['wr_datetime'])) ?></span>
        </div>
    </div>

    <div id="bo_v_atc" class="leading-relaxed text-slate-700 mb-12">
        <?php
        if (isset($view['file']) && is_array($view['file'])) {
            foreach($view['file'] as $file) {
                if (isset($file['view']) && $file['view']) {
                    echo '<div class="gallery-img-item mb-4">'.get_view_thumbnail($file['view']).'</div>';
                }
            }
        }
        ?>
        <div class="prose prose-slate max-w-none mt-6 break-all">
            <?php echo get_view_thumbnail($view['content']); ?>
        </div>
    </div>

    <div class="custom-btn-group flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-8 mb-10">
        <div class="flex gap-2">
            <a href="<?php echo $list_href ?>" class="btn-indigo">목록보기</a>
            <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="btn-slate">글쓰기</a><?php } ?>
        </div>
        <div class="flex gap-4 text-xs font-bold text-slate-400">
            <?php if ($prev_href) { ?><a href="<?php echo $prev_href ?>">이전글</a><?php } ?>
            <?php if ($next_href) { ?><a href="<?php echo $next_href ?>">다음글</a><?php } ?>
        </div>
    </div>

    <div class="p-4 md:p-8 rounded-2xl bg-slate-50 border border-slate-100 comment-mobile-fix">
        <h3 class="font-black mb-6">Comments</h3>
        <?php include_once(G5_BBS_PATH.'/view_comment.php'); ?>
    </div>
</div>

<style>
    /* 모든 모바일 강제 스타일을 무력화하는 !important 처리 */
    #custom_view_wrap { overflow-x: hidden !important; width: 100% !important; box-sizing: border-box !important; }
    
    /* 이미지 리사이징 */
    #bo_v_atc img, .gallery-img-item img { max-width: 100% !important; height: auto !important; display: block !important; margin: 0 auto !important; }

    /* 버튼 스타일 */
    .btn-indigo { background: #4f46e5 !important; color: #fff !important; padding: 10px 20px !important; border-radius: 8px !important; font-weight: bold !important; font-size: 14px !important; display: inline-block !important; }
    .btn-slate { background: #f1f5f9 !important; color: #475569 !important; padding: 10px 20px !important; border-radius: 8px !important; font-weight: bold !important; font-size: 14px !important; display: inline-block !important; }

    /* 모바일 댓글창 완전 재구성 */
    @media (max-width: 768px) {
        .comment-mobile-fix #bo_vc_w { width: 100% !important; display: block !important; margin: 0 !important; }
        .comment-mobile-fix textarea { width: 100% !important; border: 1px solid #ddd !important; border-radius: 8px !important; padding: 10px !important; min-height: 100px !important; font-size: 14px !important; }
        
        /* 이름, 비밀번호 입력칸 */
        .comment-mobile-fix .bo_vc_w_info { display: flex !important; flex-direction: column !important; gap: 8px !important; padding: 0 !important; margin: 10px 0 !important; }
        .comment-mobile-fix .bo_vc_w_info input { width: 100% !important; height: 45px !important; border: 1px solid #ddd !important; border-radius: 8px !important; float: none !important; }
        
        /* 캡차(숫자입력) */
        .comment-mobile-fix #captcha { display: flex !important; flex-direction: column !important; align-items: flex-start !important; gap: 10px !important; padding: 0 !important; }
        .comment-mobile-fix #captcha_key { width: 100% !important; height: 45px !important; border: 1px solid #ddd !important; border-radius: 8px !important; }
        
        /* 댓글등록 버튼 */
        .comment-mobile-fix .btn_submit { width: 100% !important; height: 50px !important; background: #4f46e5 !important; color: #fff !important; font-size: 16px !important; font-weight: 800 !important; border-radius: 10px !important; margin-top: 15px !important; display: block !important; }
        
        /* 기존 겹치는 요소 숨김 */
        #bo_vc_send { display: none !important; }
    }
</style>