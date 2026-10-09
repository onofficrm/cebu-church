<?php
if (!defined('_GNUBOARD_')) exit; 
add_stylesheet('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">', 0);
?>

<div class="max-w-5xl mx-auto px-4 py-6 md:py-16 font-sans text-slate-900 overflow-x-hidden">
    <div class="mb-8 space-y-4">
        <h2 class="text-xl md:text-4xl font-black text-slate-900 break-all leading-tight">
            <?php echo get_text($view['wr_subject']); ?>
        </h2>
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 text-xs md:text-sm text-slate-400">
            <span><?php echo $view['name'] ?></span>
            <span><?php echo date("Y.m.d", strtotime($view['wr_datetime'])) ?></span>
        </div>
    </div>

    <div id="bo_v_atc" class="leading-relaxed text-slate-700 break-all mb-12">
        <?php
        if (isset($view['file']) && is_array($view['file'])) {
            foreach($view['file'] as $file) {
                if (isset($file['view']) && $file['view']) {
                    echo '<div class="gallery-img-wrapper mb-4">'.get_view_thumbnail($file['view']).'</div>';
                }
            }
        }
        ?>
        <div class="prose prose-slate max-w-none mt-6">
            <?php echo get_view_thumbnail($view['content']); ?>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-8 mb-12">
        <div class="flex gap-2">
            <a href="<?php echo $list_href ?>" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg font-bold text-sm shadow-md">목록</a>
            <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-lg font-bold text-sm">글쓰기</a><?php } ?>
        </div>
        <div class="flex gap-3 text-xs font-bold text-slate-400">
            <?php if ($prev_href) { ?><a href="<?php echo $prev_href ?>">이전글</a><?php } ?>
            <?php if ($next_href) { ?><a href="<?php echo $next_href ?>">다음글</a><?php } ?>
        </div>
    </div>

    <div class="p-4 md:p-8 rounded-2xl bg-slate-50 border border-slate-100">
        <h3 class="font-black mb-6">Comments</h3>
        <div id="view_comment_wrapper" class="mobile-fix">
            <?php include_once(G5_BBS_PATH.'/view_comment.php'); ?>
        </div>
    </div>
</div>

<style>
    /* 1. 이미지 및 테이블 강제 리사이징 */
    #bo_v_atc img, .gallery-img-wrapper img { max-width: 100% !important; height: auto !important; display: block !important; margin: 0 auto !important; }
    #bo_v_atc table { display: block !important; width: 100% !important; overflow-x: auto !important; }

    /* 2. 모바일 깨짐 방지 (그누보드 모바일 스타일 강제 오버라이드) */
    @media (max-width: 768px) {
        .mobile-fix #bo_vc_w { width: 100% !important; margin: 0 !important; }
        .mobile-fix textarea { width: 100% !important; border-radius: 8px !important; padding: 10px !important; box-sizing: border-box !important; }
        .mobile-fix .bo_vc_w_info { display: flex !important; flex-direction: column !important; gap: 8px !important; margin-bottom: 10px !important; }
        .mobile-fix .bo_vc_w_info input { width: 100% !important; height: 45px !important; border: 1px solid #ddd !important; border-radius: 8px !important; padding: 0 10px !important; }
        .mobile-fix #captcha { display: block !important; margin-bottom: 10px !important; }
        .mobile-fix #captcha img { margin-bottom: 10px !important; }
        .mobile-fix #captcha_key { width: 100% !important; height: 45px !important; border-radius: 8px !important; }
        .mobile-fix .btn_submit { width: 100% !important; height: 50px !important; margin-top: 10px !important; background: #4f46e5 !important; border: none !important; color: #fff !important; font-weight: bold !important; border-radius: 8px !important; }
        
        /* 캡처에서 보인 '글 등록' 버튼 겹침 및 링크 텍스트 수정 */
        .mobile-fix #bo_vc_send { display: block !important; width: 100% !important; }
    }
</style>