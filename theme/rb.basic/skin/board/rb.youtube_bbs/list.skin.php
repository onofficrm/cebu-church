<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// Tailwind CSS CDN & Fonts & Icons
add_stylesheet('<script src="https://cdn.tailwindcss.com"></script>', 0);
add_stylesheet('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">', 0);
add_stylesheet('<script src="https://unpkg.com/lucide@latest"></script>', 0);

// 썸네일 설정
$thumb_width = 480; 
$thumb_height = 270;

// [함수] 유튜브 ID 추출 (홈화면 로직과 동일하게 적용)
function get_youtube_video_id_from_skin($url) {
    if (!$url) return '';
    $url = trim($url); // 앞뒤 공백 제거
    $url = html_entity_decode($url); // HTML 엔티티 디코딩 (중요: &amp; 등으로 깨지는 문제 방지)
    
    // 1. 입력값이 11자리 ID 그 자체인 경우
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
        return $url;
    }

    // 2. iframe 태그가 들어왔을 경우 src 속성만 추출
    if (preg_match('/src=["\']([^"\']+)["\']/', $url, $match)) {
        $url = $match[1];
    }
    
    // 3. 유튜브 ID 추출 정규식 (다양한 URL 포맷 지원)
    if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/|.+\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return $matches[1];
    }
    
    return '';
}

// [함수] 썸네일 URL 생성 (홈화면 로직)
function get_youtube_thumbnail_url_from_skin($id) {
    if ($id) {
        return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
    }
    return "";
}
?>

<div class="rb_board_wrap py-12 px-4 sm:px-6 max-w-[1600px] mx-auto font-sans">
    
    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3 group">
            <div class="relative flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 text-white shadow-lg shadow-red-200 group-hover:scale-105 transition-transform duration-300">
                <i data-lucide="play" class="w-6 h-6 fill-current ml-1"></i>
                <div class="absolute inset-0 rounded-2xl ring-1 ring-white/20"></div>
            </div>
            <a href="<?php echo $board_skin_url ?>" class="hover:text-red-600 transition-colors"><?php echo $board['bo_subject'] ?></a>
        </h1>
        
        <div class="flex flex-wrap items-center gap-3">
            <?php if ($admin_href) { ?>
            <a href="<?php echo $admin_href ?>" class="btn-tool" title="설정">
                <i data-lucide="settings" class="w-5 h-5"></i>
            </a>
            <?php } ?>
            
            <button type="button" class="btn_bo_sch btn-tool" title="검색">
                <i data-lucide="search" class="w-5 h-5"></i>
            </button>

            <?php if ($write_href) { ?>
            <a href="<?php echo $write_href ?>" class="flex items-center gap-2 bg-slate-900 hover:bg-black text-white pl-4 pr-5 py-2.5 rounded-full font-semibold shadow-xl shadow-slate-200 hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 ml-2">
                <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                </div>
                <span class="text-sm">영상 업로드</span>
            </a>
            <?php } ?>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8 bg-white p-2 rounded-xl border border-slate-100 shadow-sm">
        
        <!-- Total Count -->
        <div class="px-4 py-2 text-sm text-slate-500 font-medium">
            전체 <span class="text-slate-900 font-bold text-base"><?php echo number_format($total_count) ?></span> 개의 영상
        </div>

        <div class="flex items-center gap-4">
             <?php if ($is_category) { ?>
            <div class="flex gap-1 overflow-x-auto no-scrollbar max-w-[200px] sm:max-w-md px-2">
                <a href="<?php echo $board_skin_url ?>" class="cate-tab <?php echo (!$sca) ? 'active' : ''; ?>">전체</a>
                <?php 
                $categories = explode('|', $board['bo_category_list']);
                foreach ($categories as $category) {
                    $active_class = ($sca == $category) ? 'active' : '';
                ?>
                    <a href="<?php echo get_pretty_url($bo_table, '', 'sca='.urlencode($category)); ?>" class="cate-tab <?php echo $active_class ?>">
                        <?php echo $category ?>
                    </a>
                <?php } ?>
            </div>
            <?php } ?>

            <?php if ($is_checkbox) { ?>
            <div class="h-6 w-px bg-slate-200 mx-1 hidden sm:block"></div>
            <label class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 px-3 py-2 rounded-lg transition-colors mr-2">
                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900 cursor-pointer">
                <span class="text-xs sm:text-sm text-slate-600 font-bold">전체선택</span>
            </label>
            <?php } ?>
        </div>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 gap-y-10">
        <?php
        for ($i=0; $i<count($list); $i++) {
            
            // --------------------------------------------------------------------------------
            // 썸네일 추출 로직 개선 (홈화면과 동일하게 wr_1 체크 추가)
            // --------------------------------------------------------------------------------
            $yt_id = '';
            $thumb_src = '';

            // 1. wr_1 (여분필드 1번) 확인 - 홈화면에서 가장 먼저 체크하는 필드
            if (isset($list[$i]['wr_1']) && trim($list[$i]['wr_1'])) {
                $yt_id = get_youtube_video_id_from_skin($list[$i]['wr_1']);
            }
            
            // 2. wr_link1 (링크 1번) 확인
            if (!$yt_id && isset($list[$i]['wr_link1']) && trim($list[$i]['wr_link1'])) {
                $yt_id = get_youtube_video_id_from_skin($list[$i]['wr_link1']);
            }

            // 3. wr_link2 (링크 2번) 확인
            if (!$yt_id && isset($list[$i]['wr_link2']) && trim($list[$i]['wr_link2'])) {
                $yt_id = get_youtube_video_id_from_skin($list[$i]['wr_link2']);
            }

            // 4. 본문 내용 확인 (본문이 없을 경우 DB에서 가져오기)
            $wr_content = isset($list[$i]['wr_content']) ? $list[$i]['wr_content'] : '';
            if (!$wr_content && $write_table) {
                // 리스트에 내용이 없으면 DB 조회
                $row = sql_fetch(" select wr_content from {$write_table} where wr_id = '{$list[$i]['wr_id']}' ");
                $wr_content = $row['wr_content'];
            }
            
            if (!$yt_id && $wr_content) {
                $yt_id = get_youtube_video_id_from_skin($wr_content);
            }

            // ID가 있으면 유튜브 썸네일 생성
            if ($yt_id) {
                $thumb_src = get_youtube_thumbnail_url_from_skin($yt_id);
            } 
            else {
                // ID가 없으면 일반 첨부파일 썸네일 시도
                $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);
                if(isset($thumb['src']) && $thumb['src']) {
                     $thumb_src = $thumb['src'];
                } else {
                     // 마지막으로 본문 내 이미지 태그 확인
                     if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $wr_content, $matches)) {
                         $thumb_src = $matches[1];
                     }
                }
            }

            $has_thumb = !empty($thumb_src);
            $is_secret = strstr($list[$i]['wr_option'], 'secret');
            // --------------------------------------------------------------------------------

        ?>
        <div class="group flex flex-col gap-4">
            <!-- Card Image -->
            <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-slate-100 shadow-sm border border-slate-100 group-hover:shadow-xl group-hover:shadow-slate-200/50 transition-all duration-300 transform group-hover:-translate-y-1">
                <a href="<?php echo $list[$i]['href'] ?>" class="block w-full h-full relative z-0">
                    <?php if ($has_thumb) { ?>
                        <img src="<?php echo $thumb_src ?>" alt="<?php echo strip_tags($list[$i]['subject']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        
                        <!-- Play Button Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 bg-black/10 backdrop-blur-[1px]">
                            <div class="w-14 h-14 rounded-full bg-white/90 flex items-center justify-center text-slate-900 pl-1 shadow-2xl transform scale-75 group-hover:scale-100 transition-transform duration-300">
                                <i data-lucide="play" class="w-6 h-6 fill-current"></i>
                            </div>
                        </div>

                    <?php } else { ?>
                        <!-- Fallback Pattern -->
                        <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50 relative overflow-hidden group-hover:bg-slate-100 transition-colors">
                            <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 20px 20px;"></div>
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-300 shadow-sm mb-2 z-10">
                                <i data-lucide="video" class="w-6 h-6"></i>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 tracking-widest uppercase z-10">No Preview</span>
                        </div>
                    <?php } ?>

                    <?php if ($is_secret) { ?>
                        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm flex flex-col items-center justify-center text-white/90 z-20">
                            <i data-lucide="lock" class="w-8 h-8 mb-2 opacity-80"></i>
                            <span class="text-xs font-medium tracking-wider">비밀글</span>
                        </div>
                    <?php } ?>
                </a>

                <!-- Checkbox -->
                <?php if ($is_checkbox) { ?>
                <div class="absolute top-3 left-3 z-30 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" class="w-5 h-5 rounded border-2 border-white/80 bg-white/50 text-slate-900 focus:ring-0 checked:bg-slate-900 checked:border-transparent cursor-pointer shadow-md">
                </div>
                <?php } ?>
            </div>

            <!-- Card Body -->
            <div class="flex gap-3 px-1">
                <!-- Avatar / Category Icon -->
                <a href="<?php echo $list[$i]['href'] ?>" class="flex-shrink-0 pt-1">
                    <?php if ($list[$i]['is_notice']) { ?>
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500 ring-2 ring-red-100">
                            <i data-lucide="megaphone" class="w-5 h-5"></i>
                        </div>
                    <?php } else { ?>
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 overflow-hidden hover:bg-slate-200 hover:text-slate-600 transition-colors">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                    <?php } ?>
                </a>

                <!-- Text -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-slate-800 leading-snug line-clamp-2 mb-1.5 group-hover:text-red-600 transition-colors">
                        <a href="<?php echo $list[$i]['href'] ?>">
                            <?php echo $list[$i]['subject'] ?>
                        </a>
                        <?php if ($list[$i]['icon_new']) { ?>
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-red-500 align-top ml-1 mt-1.5 ring-2 ring-white"></span>
                        <?php } ?>
                    </h3>
                    
                    <div class="flex flex-col text-xs text-slate-500 font-medium gap-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-700 hover:underline cursor-pointer truncate max-w-[100px]"><?php echo $list[$i]['name'] ?></span>
                            <?php if ($list[$i]['ca_name']) { ?>
                                <span class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-600"><?php echo $list[$i]['ca_name'] ?></span>
                            <?php } ?>
                        </div>
                        <div class="flex items-center gap-2 text-slate-400">
                            <span>조회 <?php echo number_format($list[$i]['wr_hit']) ?></span>
                            <span class="w-0.5 h-0.5 bg-slate-300 rounded-full"></span>
                            <span><?php echo date("y.m.d", strtotime($list[$i]['wr_datetime'])) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Kebab Menu -->
                <button type="button" class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-300 hover:text-slate-600 transition-colors opacity-0 group-hover:opacity-100">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Empty State -->
    <?php if (count($list) == 0) { ?>
    <div class="flex flex-col items-center justify-center py-40 bg-slate-50 rounded-3xl border-2 border-slate-100 border-dashed text-center mx-auto max-w-2xl mt-10">
        <div class="w-20 h-20 bg-white rounded-full shadow-sm flex items-center justify-center text-slate-300 mb-6">
            <i data-lucide="film" class="w-10 h-10 opacity-50"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">아직 등록된 영상이 없습니다</h3>
        <p class="text-slate-500 mb-8 max-w-sm mx-auto">첫 번째 영상을 공유하여 커뮤니티를 시작해보세요.</p>
        <?php if ($write_href) { ?>
        <a href="<?php echo $write_href ?>" class="px-8 py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold shadow-lg shadow-slate-200 hover:-translate-y-0.5 transition-all">
            첫 영상 업로드
        </a>
        <?php } ?>
    </div>
    <?php } ?>

    <!-- Footer Controls -->
    <div class="mt-20 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-8">
        <div class="flex gap-2 w-full md:w-auto">
            <?php if ($is_checkbox) { ?>
            <button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value" class="btn-sub">삭제</button>
            <button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value" class="btn-sub">복사</button>
            <button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value" class="btn-sub">이동</button>
            <?php } ?>
        </div>
        
        <div class="w-full md:w-auto flex justify-center">
            <?php echo $write_pages; ?>
        </div>
    </div>

    </form>
    
    <!-- Search Overlay -->
    <div id="bo_sch_modal" class="fixed inset-0 z-[100] hidden items-start justify-center pt-24 bg-slate-900/30 backdrop-blur-sm transition-all duration-300 opacity-0">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl p-8 mx-4 relative transform scale-95 transition-all duration-300" id="bo_sch_content">
            <button type="button" class="bo_sch_cls absolute top-5 right-5 text-slate-400 hover:text-slate-900 p-2 rounded-full hover:bg-slate-100 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            
            <h3 class="text-2xl font-bold text-slate-900 mb-8">영상 검색</h3>
            
            <form name="fsearch" method="get" class="flex flex-col gap-4">
                <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
                <input type="hidden" name="sca" value="<?php echo $sca ?>">
                <input type="hidden" name="sop" value="and">

                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative min-w-[140px]">
                        <select name="sfl" id="sfl" class="w-full h-14 pl-5 pr-10 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-900 focus:ring-0 outline-none text-slate-700 font-bold appearance-none cursor-pointer transition-colors">
                            <?php echo get_board_sfl_select_options($sfl); ?>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                    </div>
                    
                    <div class="flex-1 relative">
                        <input type="text" name="stx" value="<?php echo stripslashes($stx); ?>" required class="w-full h-14 pl-14 pr-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-900 focus:ring-0 outline-none transition-colors font-medium text-lg placeholder-slate-400" placeholder="검색어를 입력하세요">
                        <i data-lucide="search" class="absolute left-5 top-1/2 -translate-y-1/2 w-6 h-6 text-slate-400"></i>
                    </div>
                </div>
                
                <button type="submit" class="h-14 w-full bg-slate-900 hover:bg-black text-white text-lg font-bold rounded-xl transition-all shadow-lg hover:shadow-xl mt-2">
                    검색하기
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    lucide.createIcons();

    $(document).ready(function() {
        // Search Modal Animation
        $(".btn_bo_sch").on("click", function() {
            $("#bo_sch_modal").removeClass("hidden");
            setTimeout(function() {
                $("#bo_sch_modal").removeClass("opacity-0");
                $("#bo_sch_content").removeClass("scale-95").addClass("scale-100");
            }, 10);
        });

        $(".bo_sch_cls, #bo_sch_modal").on("click", function(e) {
            if (e.target === this || $(this).hasClass("bo_sch_cls") || $(this).closest(".bo_sch_cls").length) {
                 $("#bo_sch_modal").addClass("opacity-0");
                 $("#bo_sch_content").removeClass("scale-100").addClass("scale-95");
                 setTimeout(function() {
                    $("#bo_sch_modal").addClass("hidden");
                 }, 300);
            }
        });
    });

    function all_checked(sw) {
        var f = document.fboardlist;
        for (var i=0; i<f.length; i++) {
            if (f.elements[i].name == "chk_wr_id[]") f.elements[i].checked = sw;
        }
    }

    function fboardlist_submit(f) {
        var chk_count = 0;
        for (var i=0; i<f.length; i++) {
            if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked) chk_count++;
        }
        if (!chk_count) {
            alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
            return false;
        }
        if(document.pressed == "선택삭제") {
            if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다.")) return false;
            f.removeAttribute("target");
            f.action = g5_bbs_url+"/board_list_update.php";
        }
        if(document.pressed == "선택복사" || document.pressed == "선택이동") {
            select_copy(document.pressed == "선택복사" ? "copy" : "move");
            return false;
        }
        return true;
    }

    function select_copy(sw) {
        var f = document.fboardlist;
        var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");
        f.sw.value = sw;
        f.target = "move";
        f.action = g5_bbs_url+"/move.php";
        f.submit();
    }
</script>