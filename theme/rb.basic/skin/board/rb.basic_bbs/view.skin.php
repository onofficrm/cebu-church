<?php
if (!defined('_GNUBOARD_')) exit; 
add_stylesheet('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">', 0);
?>

<div id="custom_view_wrap" class="max-w-4xl mx-auto px-4 py-6 md:py-12 font-sans text-slate-900">
    <header class="mb-8">
        <h2 class="text-2xl md:text-4xl font-black text-slate-900 break-words mb-4 leading-tight">
            <?php echo get_text($view['wr_subject']); ?>
        </h2>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 pb-5 border-b border-slate-200 text-sm text-slate-500">
            <strong class="text-slate-700"><?php echo $view['name'] ?></strong>
            <span aria-hidden="true">·</span>
            <time datetime="<?php echo date("Y-m-d", strtotime($view['wr_datetime'])) ?>"><?php echo date("Y.m.d", strtotime($view['wr_datetime'])) ?></time>
            <span aria-hidden="true">·</span>
            <span>조회 <?php echo number_format($view['wr_hit']) ?></span>
        </div>
    </header>

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
        <p class="view-image-tip text-center text-xs text-slate-400 mt-4">이미지를 누르면 원본 크기로 볼 수 있습니다.</p>
    </div>

    <nav class="custom-btn-group flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 pt-6 mb-10" aria-label="게시글 이동">
        <div class="flex gap-2">
            <a href="<?php echo $list_href ?>" class="btn-indigo">목록</a>
            <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="btn-slate">글쓰기</a><?php } ?>
            <?php if ($update_href) { ?><a href="<?php echo $update_href ?>" class="btn-slate">수정</a><?php } ?>
            <?php if ($delete_href) { ?><a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;" class="btn-slate">삭제</a><?php } ?>
        </div>
        <div class="flex gap-2 text-sm font-bold text-slate-500">
            <?php if ($prev_href) { ?><a class="post-move-link" href="<?php echo $prev_href ?>">← 이전글</a><?php } ?>
            <?php if ($next_href) { ?><a class="post-move-link" href="<?php echo $next_href ?>">다음글 →</a><?php } ?>
        </div>
    </nav>

    <?php if ($bo_table !== 'notice') { ?>
    <section class="p-4 md:p-8 rounded-2xl bg-slate-50 border border-slate-100 comment-mobile-fix">
        <h3 class="font-black mb-6">댓글</h3>
        <?php include_once(G5_BBS_PATH.'/view_comment.php'); ?>
    </section>
    <?php } ?>
</div>

<style>
    #container_title { display: none !important; }
    /* 모든 모바일 강제 스타일을 무력화하는 !important 처리 */
    #custom_view_wrap { overflow-x: hidden !important; width: 100% !important; box-sizing: border-box !important; }
    
    /* 이미지 리사이징 */
    #bo_v_atc img, .gallery-img-item img { max-width: 100% !important; height: auto !important; display: block !important; margin: 0 auto !important; cursor: zoom-in; border-radius: 10px; }

    /* 버튼 스타일 */
    .btn-indigo { background: #2563eb !important; color: #fff !important; min-height:44px; padding: 0 18px !important; border-radius: 10px !important; font-weight: bold !important; font-size: 14px !important; display: inline-flex !important; align-items:center; }
    .btn-slate { background: #f1f5f9 !important; color: #475569 !important; min-height:44px; padding: 0 18px !important; border-radius: 10px !important; font-weight: bold !important; font-size: 14px !important; display: inline-flex !important; align-items:center; }
    .post-move-link { min-height:44px; display:inline-flex; align-items:center; padding:0 10px; border-radius:8px; }
    .post-move-link:hover { background:#f8fafc; color:#2563eb; }

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var images = document.querySelectorAll('#bo_v_atc img');
    var tip = document.querySelector('.view-image-tip');
    if (!images.length && tip) tip.hidden = true;
    images.forEach(function (image) {
        image.setAttribute('tabindex', '0');
        image.setAttribute('role', 'button');
        image.setAttribute('aria-label', '이미지 원본 크기로 보기');
        var openOriginal = function () { window.open(image.currentSrc || image.src, '_blank', 'noopener'); };
        image.addEventListener('click', openOriginal);
        image.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openOriginal();
            }
        });
    });
});
</script>