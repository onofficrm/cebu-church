<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// Tailwind CSS CDN & Fonts
add_stylesheet('<script src="https://cdn.tailwindcss.com"></script>', 0);
add_stylesheet('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">', 0);
add_stylesheet('<script src="https://unpkg.com/lucide@latest"></script>', 0);
?>

<style>
    #container_title { display: none !important; }
    /* Scoped font application to avoid affecting the global header/footer if possible, 
       but for consistency we apply to the wrapper class */
    .rb_board_wrap { font-family: 'Inter', 'Noto Sans KR', sans-serif; }

    /* Scrollbar hide for category */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .board-thumb-mobile { aspect-ratio: 4 / 3; height: auto; }
</style>

<div class="rb_board_wrap py-5 md:py-12 px-4 sm:px-6 max-w-5xl mx-auto">
    
    <!-- 게시판 목록 시작 -->
    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <!-- Header Actions -->
    <div class="flex justify-between items-center gap-4 mb-5 md:mb-7">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
            <a href="<?php echo get_pretty_url($bo_table) ?>"><?php echo $board['bo_subject'] ?></a>
        </h1>
        
        <div class="flex items-center gap-2 self-end sm:self-auto">
            <?php if ($admin_href) { ?>
            <a href="<?php echo $admin_href ?>" class="p-2 text-slate-500 hover:bg-white hover:text-blue-600 hover:shadow-sm rounded-lg transition-all" title="관리자">
                <i data-lucide="settings" class="w-5 h-5"></i>
            </a>
            <?php } ?>
            
            <button type="button" class="btn_bo_sch p-2 text-slate-500 hover:bg-white hover:text-blue-600 hover:shadow-sm rounded-lg transition-all" title="검색" aria-label="게시글 검색 열기">
                <i data-lucide="search" class="w-5 h-5"></i>
            </button>
            
            <?php if ($rss_href) { ?>
            <a href="<?php echo $rss_href ?>" class="p-2 text-slate-500 hover:bg-white hover:text-orange-500 hover:shadow-sm rounded-lg transition-all" title="RSS">
                <i data-lucide="rss" class="w-5 h-5"></i>
            </a>
            <?php } ?>

            <?php if ($write_href) { ?>
            <a href="<?php echo $write_href ?>" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm shadow-blue-200 transition-all">
                <i data-lucide="pen-square" class="w-4 h-4"></i>
                <span>글쓰기</span>
            </a>
            <?php } ?>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="flex justify-between items-center bg-white px-3 py-2 rounded-xl border border-gray-100 mb-5 gap-3">
        <div class="flex items-center gap-4 w-full sm:w-auto">
            
            <?php if ($is_admin) { ?>
            <!-- Point Info Toggle: 관리자에게만 표시 -->
            <div class="relative">
                <button type="button" id="point_info_btn" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-slate-600 hover:bg-gray-50 transition-colors">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    <span>포인트정책</span>
                </button>
                
                <!-- Popup -->
                <div id="point_info_popup" class="hidden absolute z-50 top-10 left-0 w-64 bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-slate-50 px-4 py-3 border-b border-gray-100">
                        <h6 class="text-sm font-bold text-slate-800">게시판 포인트 정책</h6>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">글읽기</span>
                            <span class="font-bold text-blue-600"><?php echo number_format($board['bo_read_point']) ?> P</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">글쓰기</span>
                            <span class="font-bold text-blue-600"><?php echo number_format($board['bo_write_point']) ?> P</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">댓글</span>
                            <span class="font-bold text-blue-600"><?php echo number_format($board['bo_comment_point']) ?> P</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">다운로드</span>
                            <span class="font-bold text-red-500"><?php echo number_format($board['bo_download_point']) ?> P</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($is_admin && $is_checkbox) { ?><div class="h-4 w-[1px] bg-gray-300 hidden sm:block"></div><?php } ?>
            
            <?php if ($is_checkbox) { ?>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                <label for="chkall" class="text-sm text-slate-600 cursor-pointer select-none">전체선택</label>
            </div>
            <?php } ?>
        </div>

        <div class="text-xs sm:text-sm text-slate-500 text-right">
            전체 <span class="font-bold text-slate-800"><?php echo number_format($total_count) ?></span>건 · <?php echo $page ?>페이지
        </div>
    </div>

    <!-- Category (If exists) -->
    <?php if ($is_category) { ?>
    <div class="mb-6 relative group">
        <div class="overflow-x-auto no-scrollbar pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="flex gap-2 min-w-max">
                <a href="<?php echo get_pretty_url($bo_table) ?>" class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 whitespace-nowrap <?php echo (!$sca) ? 'bg-slate-800 text-white shadow-md' : 'bg-white text-slate-500 border border-gray-200 hover:bg-gray-50'; ?>">전체</a>
                <?php 
                $categories = explode('|', $board['bo_category_list']);
                foreach ($categories as $category) {
                    $active_class = ($sca == $category) ? 'bg-slate-800 text-white shadow-md' : 'bg-white text-slate-500 border border-gray-200 hover:bg-gray-50';
                ?>
                    <a href="<?php echo get_pretty_url($bo_table, '', 'sca='.urlencode($category)); ?>" class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 whitespace-nowrap <?php echo $active_class ?>">
                        <?php echo $category ?>
                    </a>
                <?php } ?>
            </div>
        </div>
        <div class="absolute right-0 top-0 bottom-2 w-12 bg-gradient-to-l from-[#f8fafc] to-transparent sm:hidden pointer-events-none"></div>
    </div>
    <?php } ?>

    <!-- Post List -->
    <div class="space-y-3 mb-8">
        <?php
        for ($i=0; $i<count($list); $i++) {
            $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height'], false, true);
            $is_secret = strstr($list[$i]['wr_option'], 'secret');
        ?>
        <article class="group relative bg-white border border-gray-200 rounded-xl p-4 sm:p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row gap-4">
            
            <?php if ($is_checkbox) { ?>
            <div class="absolute top-4 right-4 sm:static sm:top-auto sm:right-auto flex items-start pt-1">
                <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
            </div>
            <?php } ?>

            <div class="flex-1 flex flex-col justify-between min-w-0">
                <div>
                    <!-- Meta Top -->
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                        <?php if ($list[$i]['is_notice']) { ?>
                            <span class="text-blue-600 font-bold">공지</span>
                        <?php } else { ?>
                            <span class="font-mono text-slate-400"><?php echo $list[$i]['num'] ?></span>
                        <?php } ?>
                        
                        <?php if ($is_category && $list[$i]['ca_name']) { ?>
                            <span class="w-[1px] h-3 bg-gray-300 mx-1"></span>
                            <span class="font-medium text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded"><?php echo $list[$i]['ca_name'] ?></span>
                        <?php } ?>
                        
                        <span class="hidden sm:inline text-slate-400">•</span>
                        <span class="text-slate-400"><?php echo $list[$i]['datetime2'] ?></span>
                    </div>

                    <!-- Subject -->
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-1 group-hover:text-blue-600 transition-colors line-clamp-1 flex items-center">
                        <?php if ($is_secret) { ?>
                            <i data-lucide="lock" class="w-3.5 h-3.5 mr-1.5 text-slate-400"></i>
                        <?php } ?>
                        
                        <a href="<?php echo $list[$i]['href'] ?>" class="align-middle">
                            <?php echo $list[$i]['subject'] ?>
                        </a>

                        <?php if ($list[$i]['icon_new']) { ?>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 ml-1.5">NEW</span>
                        <?php } ?>
                    </h3>

                    <!-- Content Summary (If not secret) -->
                    <p class="hidden sm:block text-sm text-slate-500 line-clamp-1 mb-3 leading-relaxed">
                        <?php 
                        if ($is_secret) { 
                            echo "비밀글입니다.";
                        } else {
                            echo strip_tags($list[$i]['wr_content']); 
                        }
                        ?>
                    </p>
                </div>

                <!-- Meta Bottom -->
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs sm:text-sm text-slate-500 mt-1">
                    <span class="font-medium text-slate-700"><?php echo $list[$i]['name'] ?></span>
                    
                    <div class="flex items-center gap-3 ml-auto sm:ml-0">
                        <div class="flex items-center gap-1" title="조회수">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            <span><?php echo number_format($list[$i]['wr_hit']) ?></span>
                        </div>
                        <?php if ($list[$i]['wr_comment'] > 0) { ?>
                        <div class="flex items-center gap-1 text-slate-700 font-medium" title="댓글">
                            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                            <span><?php echo number_format($list[$i]['wr_comment']) ?></span>
                        </div>
                        <?php } ?>
                        <?php if ($list[$i]['wr_good'] > 0) { ?>
                        <div class="flex items-center gap-1 text-red-500" title="추천">
                            <i data-lucide="thumbs-up" class="w-3.5 h-3.5"></i>
                            <span><?php echo number_format($list[$i]['wr_good']) ?></span>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Thumbnail (Desktop) -->
            <?php if ($thumb['src']) { ?>
            <div class="hidden sm:block w-[120px] h-[90px] flex-shrink-0">
                <a href="<?php echo $list[$i]['href'] ?>">
                    <img src="<?php echo $thumb['src'] ?>" alt="<?php echo $thumb['alt'] ?>" class="w-full h-full object-cover rounded-lg border border-gray-100">
                </a>
            </div>
            
            <!-- Thumbnail (Mobile) -->
            <div class="board-thumb-mobile block sm:hidden w-full mt-2 order-last overflow-hidden rounded-lg">
                <a href="<?php echo $list[$i]['href'] ?>">
                    <img src="<?php echo $thumb['src'] ?>" alt="<?php echo $thumb['alt'] ?>" class="w-full h-full object-cover rounded-lg">
                </a>
            </div>
            <?php } ?>

        </article>
        <?php } ?>

        <?php if (count($list) == 0) { ?>
        <div class="py-20 text-center bg-white rounded-xl border border-gray-200 border-dashed">
            <div class="text-slate-400 mb-2">게시물이 없습니다.</div>
            <?php if ($write_href) { ?>
            <a href="<?php echo $write_href ?>" class="text-blue-600 hover:underline text-sm font-medium">첫 번째 글을 작성해보세요</a>
            <?php } ?>
        </div>
        <?php } ?>
    </div>

    <!-- Bottom Actions -->
    <div class="flex flex-col-reverse md:flex-row justify-between items-center gap-6">
        <!-- Admin Batch Actions -->
        <div class="flex gap-2 w-full md:w-auto">
            <?php if ($is_checkbox) { ?>
            <button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value" class="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors">선택삭제</button>
            <button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value" class="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-gray-50 transition-colors">선택복사</button>
            <button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value" class="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-gray-50 transition-colors">선택이동</button>
            <?php } ?>
        </div>
        
        <!-- Pagination -->
        <div class="flex items-center justify-center gap-1">
            <?php echo $write_pages; ?>
        </div>

        <!-- Mobile Write Button -->
        <?php if ($write_href) { ?>
        <div class="md:hidden w-full">
            <a href="<?php echo $write_href ?>" class="block text-center w-full bg-blue-600 text-white py-3 rounded-lg font-bold shadow-lg shadow-blue-200">
                글 등록하기
            </a>
        </div>
        <?php } ?>
    </div>

    </form>
    
    <!-- Search Modal -->
    <div id="bo_sch_modal" class="hidden fixed inset-0 z-[100] flex items-start justify-center pt-20 bg-black/50 backdrop-blur-sm">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl p-6 mx-4 relative">
            <button type="button" class="bo_sch_cls absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            <h3 class="text-xl font-bold text-slate-800 mb-6">게시글 검색</h3>
            
            <form name="fsearch" method="get">
                <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
                <input type="hidden" name="sca" value="<?php echo $sca ?>">
                <input type="hidden" name="sop" value="and">

                <div class="flex flex-col sm:flex-row gap-3">
                    <select name="sfl" id="sfl" class="px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-slate-700 min-w-[120px]">
                        <?php echo get_board_sfl_select_options($sfl); ?>
                    </select>
                    <div class="flex-1 relative">
                        <input type="text" name="stx" value="<?php echo stripslashes($stx); ?>" required class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="검색어를 입력해주세요">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition-colors">
                        검색
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    $(document).ready(function() {
        // Search Modal Toggle
        $(".btn_bo_sch").on("click", function() {
            $("#bo_sch_modal").fadeIn(200).removeClass("hidden").css("display", "flex");
        });
        $(".bo_sch_cls, #bo_sch_modal").on("click", function(e) {
            if (e.target === this || $(this).hasClass("bo_sch_cls") || $(this).closest(".bo_sch_cls").length) {
                 $("#bo_sch_modal").fadeOut(200);
            }
        });
        $("#bo_sch_modal > div").on("click", function(e) {
            e.stopPropagation();
        });

        // Point Info Toggle
        $('#point_info_btn').click(function(event) {
            event.stopPropagation(); 
            $('#point_info_popup').toggleClass('hidden');
            $(this).toggleClass('bg-blue-50 text-blue-700');
        });

        $(document).click(function(event) {
            if (!$(event.target).closest('#point_info_btn, #point_info_popup').length) {
                $('#point_info_popup').addClass('hidden');
                $('#point_info_btn').removeClass('bg-blue-50 text-blue-700');
            }
        });
    });

    function all_checked(sw) {
        var f = document.fboardlist;
        for (var i=0; i<f.length; i++) {
            if (f.elements[i].name == "chk_wr_id[]")
                f.elements[i].checked = sw;
        }
    }

    function fboardlist_submit(f) {
        var chk_count = 0;
        for (var i=0; i<f.length; i++) {
            if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
                chk_count++;
        }

        if (!chk_count) {
            alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
            return false;
        }

        if(document.pressed == "선택복사") {
            select_copy("copy");
            return;
        }

        if(document.pressed == "선택이동") {
            select_copy("move");
            return;
        }

        if(document.pressed == "선택삭제") {
            if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다."))
                return false;
            f.removeAttribute("target");
            f.action = g5_bbs_url+"/board_list_update.php";
        }
        return true;
    }

    function select_copy(sw) {
        var f = document.fboardlist;
        if (sw == 'copy') str = "복사";
        else str = "이동";
        var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");
        f.sw.value = sw;
        f.target = "move";
        f.action = g5_bbs_url+"/move.php";
        f.submit();
    }
</script>