<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// 스타일시트 로드 (버전 파라미터 추가로 캐시 갱신 유도)
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?ver='.G5_SERVER_TIME.'">', 0);
add_stylesheet('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">', 0);

$bo_gallery_width = isset($board['bo_gallery_width']) ? $board['bo_gallery_width'] : 400;
$bo_gallery_height = isset($board['bo_gallery_height']) ? $board['bo_gallery_height'] : 300;
?>

<div class="gallery_modern_wrap">

    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <!-- 상단 툴바 -->
    <div class="gall_toolbar">
        <div class="gall_total">
            Total <b><?php echo number_format($total_count) ?></b>건 <span style="color:#ddd; margin:0 5px;">|</span> <?php echo $page ?> 페이지
        </div>
        
        <div class="gall_btns">
            <?php if ($admin_href) { ?>
            <button type="button" class="btn_gall" onclick="window.open('<?php echo $admin_href ?>');" title="관리자">
                <i class="fas fa-cog"></i> 관리
            </button>
            <?php } ?>
            
            <button type="button" class="btn_gall btn_search_open" title="검색">
                <i class="fas fa-search"></i> 검색
            </button>

            <?php if ($rss_href) { ?>
            <button type="button" class="btn_gall" onclick="window.open('<?php echo $rss_href ?>');" title="RSS">
                <i class="fas fa-rss"></i> RSS
            </button>
            <?php } ?>
            
            <?php if ($write_href) { ?>
            <button type="button" class="btn_gall primary" onclick="location.href='<?php echo $write_href ?>';">
                <i class="fas fa-pen"></i> 글쓰기
            </button>
            <?php } ?>
        </div>
    </div>
    
    <!-- 옵션 (선택박스) -->
    <?php if ($is_checkbox) { ?>
    <div style="margin-bottom:15px; text-align:right;">
        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
        <label for="chkall" style="cursor:pointer; font-size:13px; color:#666; margin-left:5px;">전체선택</label>
    </div>
    <?php } ?>

    <!-- 갤러리 리스트 그리드 -->
    <div class="gall_grid">
        <?php 
        for ($i=0; $i<count($list); $i++) { 
            $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $bo_gallery_width, $bo_gallery_height, false, true);
            
            if($thumb['src']) {
                $img_src = $thumb['src'];
            } else { 
                $img_src = G5_THEME_URL.'/rb.img/no_image.png';
                // 대체 이미지 로직 (테마 이미지가 없을 경우)
                if (!file_exists($img_src)) $img_src = "https://dummyimage.com/600x400/f0f0f0/cccccc&text=No+Image"; 
            }
            
            $wr_href = $list[$i]['href'];
            $wr_content = preg_replace("/<(.*?)\>/","",$list[$i]['wr_content']);
            $wr_content = preg_replace("/&nbsp;/","",$wr_content);
            $wr_content = get_text($wr_content);
            $wr_content = cut_str($wr_content, 100); 
        ?>
        
        <div class="gall_card">
            <!-- 이미지 -->
            <a href="<?php echo $wr_href ?>" class="card_img">
                <img src="<?php echo $img_src ?>" alt="<?php echo $thumb['alt'] ?>">
                
                <!-- 라벨 -->
                <div class="card_labels">
                    <?php if ($list[$i]['icon_new']) echo "<span class=\"label_ico label_new\">N</span>"; ?>
                    <?php if ($list[$i]['icon_hot']) echo "<span class=\"label_ico label_hot\">H</span>"; ?>
                    <?php if (strstr($list[$i]['wr_option'], 'secret')) echo "<span class=\"label_ico\" style=\"background:#555;\"><i class=\"fas fa-lock\"></i></span>"; ?>
                </div>
            </a>
            
            <!-- 체크박스 -->
            <?php if ($is_checkbox) { ?>
            <div class="card_chk">
                <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                <label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
            </div>
            <?php } ?>

            <!-- 내용 -->
            <div class="card_body">
                <div class="card_meta">
                    <span class="card_cate"><?php echo $list[$i]['ca_name'] ? $list[$i]['ca_name'] : '일반'; ?></span>
                    <span class="card_date"><?php echo date("Y.m.d", strtotime($list[$i]['wr_datetime'])) ?></span>
                </div>

                <h3 class="card_title">
                    <a href="<?php echo $wr_href ?>">
                        <?php echo $list[$i]['subject'] ?>
                    </a>
                </h3>
                
                <div class="card_desc">
                    <?php if (strstr($list[$i]['wr_option'], 'secret')) { ?>
                        <span style="color:#999;"><i class="fas fa-lock"></i> 비밀글입니다.</span>
                    <?php } else { ?>
                        <?php echo $wr_content ?>
                    <?php } ?>
                </div>

                <div class="card_footer">
                    <div class="card_writer">
                        <i class="fas fa-user-circle"></i> <?php echo $list[$i]['name'] ?>
                    </div>
                    <div class="card_stats">
                        <span title="조회"><i class="far fa-eye"></i> <?php echo number_format($list[$i]['wr_hit']); ?></span>
                        <?php if($list[$i]['wr_comment'] > 0) { ?>
                        <span title="댓글" style="color:var(--gall-point)"><i class="fas fa-comment"></i> <?php echo $list[$i]['wr_comment']; ?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    
    <?php if (count($list) == 0) { echo "<div class=\"gall_no_data\">게시물이 없습니다.</div>"; } ?>

    <!-- 하단 관리 버튼 -->
    <div class="gall_btm">
        <div class="btm_left">
            <?php if ($is_checkbox) { ?>
                <button type="submit" name="btn_submit" class="btn_gall" value="선택삭제" onclick="document.pressed=this.value">선택삭제</button>
                <button type="submit" name="btn_submit" class="btn_gall" value="선택복사" onclick="document.pressed=this.value">선택복사</button>
                <button type="submit" name="btn_submit" class="btn_gall" value="선택이동" onclick="document.pressed=this.value">선택이동</button>
            <?php } ?>
        </div>
        <div class="btm_right">
             <?php if ($write_href) { ?>
            <button type="button" class="btn_gall primary" onclick="location.href='<?php echo $write_href ?>';">글쓰기</button>
            <?php } ?>
        </div>
    </div>
    
    <!-- 페이지네이션 -->
    <div class="gall_pg">
        <?php echo $write_pages; ?>
    </div>

    </form>
</div>

<!-- 검색 모달 -->
<div class="search_modal">
    <div class="search_bg"></div>
    <div class="search_box">
        <button type="button" class="search_close"><i class="fas fa-times"></i></button>
        <form name="fsearch" method="get" class="search_form">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sca" value="<?php echo $sca ?>">
            <input type="hidden" name="sop" value="and">
            
            <h3>게시물 검색</h3>
            
            <div style="margin-bottom:10px;">
                <select name="sfl" id="sfl" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                    <?php echo get_board_sfl_select_options($sfl); ?>
                </select>
            </div>
            
            <div class="search_row">
                <input type="text" name="stx" value="<?php echo stripslashes($stx); ?>" required class="search_input" placeholder="검색어를 입력하세요">
                <button type="submit" class="search_btn">검색</button>
            </div>
        </form>
    </div>
</div>

<script>
// 검색 모달
$(".btn_search_open").on("click", function() {
    $(".search_modal").fadeIn(200);
});
$('.search_bg, .search_close').click(function(){
    $(".search_modal").fadeOut(200);
});

<?php if ($is_checkbox) { ?>
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
<?php } ?>
</script>
