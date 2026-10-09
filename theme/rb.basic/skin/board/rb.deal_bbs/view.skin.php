<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?ver='.G5_TIME_YMDHIS.'">', 0);


//필드분할
$wr_3 = isset($view["wr_3"]) ? explode("|", $view["wr_3"]) : [];
$mb = get_member($view['mb_id']);

?>
<style>
#scroll_container {margin-top: 20px;}
#scroll_container .rb_bbs_top{display: none;}
</style>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<div class="rb_bbs_wrap" style="width:<?php echo $width; ?>">
       
    <div class="btns_gr_wrap">
      
       <!-- $rb_core['sub_width'] 는 반드시 포함해주세요 (환경설정 > 서브가로폭에 따른 버튼의 위치 설정) -->
       <div class="sub" style="width:<?php echo $rb_core['sub_width'] ?>px;">
            
            <div class="btns_gr">
               <?php if ($admin_href) { ?>
               <button type="button" class="fl_btns" onclick="window.open('<?php echo $admin_href ?>');">
               <img src="<?php echo $board_skin_url ?>/img/ico_set.svg">
               <span class="tooltips">관리</span>
               </button>
               <?php } ?>
               
               <?php if ($scrap_href) { ?>
               <a class="fl_btns" href="<?php echo $scrap_href;  ?>" target="_blank" onclick="win_scrap(this.href); return false;">
               <img src="<?php echo $board_skin_url ?>/img/ico_scr.svg">
               <span class="tooltips">스크랩</span>
               </a>
               <?php } ?>
               
               <?php if ($list_href) { ?>
               <button type="button" class="fl_btns" onclick="location.href='<?php echo $list_href ?>';">
               <img src="<?php echo $board_skin_url ?>/img/ico_list.svg">
               <span class="tooltips">목록</span>
               </button>
               <?php } ?>

               
               <?php if ($write_href) { ?>
               <button type="button" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
               <img src="<?php echo $board_skin_url ?>/img/ico_write.svg">
               <span class="tooltips">글 등록</span>
               </button>
               <?php } ?>
               
            </div>
            
            <div class="cb"></div>
        </div>
    </div>
    
    
    <div class="bbs_sv_wrap">
       
       
        <ul class="bbs_sv_wrap_ul2">
            <div class="gap_btm_bd flex_top_cat">
                
                <?php if ($category_name) { ?>
                <li class="view_info_span"><?php echo $view['ca_name'] ?></li>
                <?php } ?>
                
                <li class="rb_bbs_for_mem_names">
                        <?php
                        $view['icon_new'] = "";
                        if ($view['wr_datetime'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - ($board['bo_new'] * 3600)))
                            $view['icon_new'] = "<span class=\"lb_ico_new\">신규</span>";
                        $view['icon_hot'] = "";
                        if ($view['wr_hit'] >= $board['bo_hot'])
                            $view['icon_hot'] = "<span class=\"lb_ico_hot\">인기</span>";

                        echo $view['icon_new']; //뉴아이콘
                        echo $view['icon_hot']; //인기아이콘 
                        ?>
                </li>

                
                <div id="bo_v_share">
                        <?php include_once(G5_SNS_PATH."/view.sns.skin.php"); ?>
                        <ul class="copy_urls">
                            <li>
                                <a href="javascript:void(0);" id="data-copy">
                                   <img src="<?php echo $board_skin_url ?>/img/ico_sha.png" alt="공유링크 복사" width="32">
                                </a>
                            </li>
                            <?php
                            $currents_url = G5_URL.$_SERVER['REQUEST_URI'];
                            ?>
                            <input type="hidden" id="data-area" class="data-area" value="<?php echo $currents_url ?>">
                            <script>
                                $(document).ready(function() {

                                    $('#data-copy').click(function() {
                                        $('#data-area').attr('type', 'text'); // 화면에서 hidden 처리한 input box type을 text로 일시 변환
                                        $('#data-area').select(); // input에 담긴 데이터를 선택
                                        var copy = document.execCommand('copy'); // clipboard에 데이터 복사
                                        $('#data-area').attr('type', 'hidden'); // input box를 다시 hidden 처리
                                        if (copy) {
                                            alert("공유 링크가 복사 되었습니다."); // 사용자 알림
                                        }
                                    });

                                });
                            </script>
                        </ul>

                </div>
                
                
            </div>
            <div class="gap_btm_bd">

                <ul class="font-B view_info_tit"><?php echo get_text($view['wr_subject']);?></ul>
                
                <?php if(isset($view['wr_8']) && $view['wr_8'] == "판매완료") { ?>
                    <ul class="view_info_sub">판매 완료된 상품 입니다.</ul>
                <?php } else if(isset($view['wr_8']) && $view['wr_8'] == "예약중") { ?>
                    <ul class="view_info_sub">거래 예약중인 상품 입니다.</ul>
                <?php } else { ?>
                    <ul class="view_info_sub">판매금액</ul>
                <?php } ?>
                
                <?php if(isset($view['wr_6']) && $view['wr_6'] || isset($view['wr_7']) && $view['wr_7']) { ?>
                <ul class="font-B view_info_pri1 main_color">
                    <?php if(isset($view['wr_7']) && $view['wr_7']) { ?>
                    <li><?php if(isset($view['wr_8']) && $view['wr_8'] == "판매완료") { ?><strike style="opacity:0.5; color:#999"><?php } ?>별도협의<?php if(isset($view['wr_8']) && $view['wr_8'] == "판매완료") { ?></strike><?php } ?></li>
                    <?php } else { ?>
                    <li><?php if(isset($view['wr_8']) && $view['wr_8'] == "판매완료") { ?><strike style="opacity:0.5; color:#999"><?php } ?><?php echo isset($view['wr_6']) ? number_format($view['wr_6']) : ''; ?>원<?php if(isset($view['wr_8']) && $view['wr_8'] == "판매완료") { ?></strike><?php } ?></li>
                    <?php } ?>
                </ul>
                <?php } ?>
                
                
                
                <ul class="view_info_btns">
                    
                    <!--  추천 비추천 시작 { -->
                    <?php if ( $good_href || $nogood_href) { ?>
                    <div id="bo_v_act">
                        <?php if ($good_href) { ?>
                        <span class="bo_v_act_gng">
                            <a href="<?php if(!$is_member) { ?>javascript:alert('로그인 후 이용하실 수 있습니다.');<?php } else { ?><?php echo $good_href.'&amp;'.$qstr ?><?php } ?>" id="good_button" class="bo_v_good">
                            <svg width="18" height="18" viewBox="0 0 21 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.5722 1.54713C18.6672 2.76813 20.1412 5.24713 20.0762 8.13913C19.9952 11.7491 17.1862 14.9331 12.3972 17.7771C11.6872 18.1991 10.9392 18.7461 10.0782 18.7461C9.23319 18.7461 8.45319 18.1891 7.75819 17.7761C2.97119 14.9331 0.161193 11.7481 0.0801927 8.13913C0.0151927 5.24713 1.48919 2.76913 3.58419 1.54713C5.54419 0.406128 8.00619 0.399127 10.0782 2.08413C12.1502 0.399127 14.6122 0.405128 16.5722 1.54713ZM15.5652 3.27613C14.1712 2.46413 12.4292 2.49313 10.9212 4.01913C10.8108 4.13033 10.6794 4.21859 10.5348 4.27882C10.3901 4.33905 10.2349 4.37005 10.0782 4.37005C9.92148 4.37005 9.76631 4.33905 9.62163 4.27882C9.47695 4.21859 9.34562 4.13033 9.23519 4.01913C7.72719 2.49313 5.98519 2.46413 4.59119 3.27613C3.14719 4.11813 2.03119 5.90413 2.08019 8.09613C2.13619 10.6071 4.12019 13.2901 8.78019 16.0581C9.18819 16.3011 9.61419 16.6121 10.0782 16.7411C10.5422 16.6121 10.9682 16.3011 11.3762 16.0581C16.0362 13.2901 18.0202 10.6081 18.0762 8.09513C18.1262 5.90513 17.0092 4.11813 15.5652 3.27613Z" fill="#09244B"/>
                            </svg>
                             <span><?php echo number_format($view['wr_good']) ?></span></a>
                            <b id="bo_v_act_good" class="font-R"></b>
                        </span>
                        <?php } ?>

                    </div>
                    <?php } else {
                        if($board['bo_use_good'] || $board['bo_use_nogood']) {
                    ?>
                    <div id="bo_v_act">
                        <?php if($board['bo_use_good']) { ?>
                            <span class="bo_v_act_gng">

                                <a href="<?php if(!$is_member) { ?>javascript:alert('로그인 후 이용하실 수 있습니다.');<?php } else { ?>javascript:void(0);<?php } ?>" class="bo_v_good">
                                <svg width="18" height="18" viewBox="0 0 21 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.5722 1.54713C18.6672 2.76813 20.1412 5.24713 20.0762 8.13913C19.9952 11.7491 17.1862 14.9331 12.3972 17.7771C11.6872 18.1991 10.9392 18.7461 10.0782 18.7461C9.23319 18.7461 8.45319 18.1891 7.75819 17.7761C2.97119 14.9331 0.161193 11.7481 0.0801927 8.13913C0.0151927 5.24713 1.48919 2.76913 3.58419 1.54713C5.54419 0.406128 8.00619 0.399127 10.0782 2.08413C12.1502 0.399127 14.6122 0.405128 16.5722 1.54713ZM15.5652 3.27613C14.1712 2.46413 12.4292 2.49313 10.9212 4.01913C10.8108 4.13033 10.6794 4.21859 10.5348 4.27882C10.3901 4.33905 10.2349 4.37005 10.0782 4.37005C9.92148 4.37005 9.76631 4.33905 9.62163 4.27882C9.47695 4.21859 9.34562 4.13033 9.23519 4.01913C7.72719 2.49313 5.98519 2.46413 4.59119 3.27613C3.14719 4.11813 2.03119 5.90413 2.08019 8.09613C2.13619 10.6071 4.12019 13.2901 8.78019 16.0581C9.18819 16.3011 9.61419 16.6121 10.0782 16.7411C10.5422 16.6121 10.9682 16.3011 11.3762 16.0581C16.0362 13.2901 18.0202 10.6081 18.0762 8.09513C18.1262 5.90513 17.0092 4.11813 15.5652 3.27613Z" fill="#09244B"/>
                                </svg>
                                <span><?php echo number_format($view['wr_good']) ?></span></a>
                                <b id="bo_v_act_good" class="font-R"></b>
                            </span>
                        <?php } ?>

                    </div>
                    <?php
                            }
                        }
                    ?>
                    <!-- }  추천 비추천 끝 -->
                    
                    
                    <?php if(isset($view['wr_8']) && $view['wr_8'] == "판매완료") { ?>
                        
                        <a href="javascript:alert('판매 완료된 상품 입니다.');" class="view_info_btns_btn1 font-R">판매완료</a>
                        
                    <?php } else { ?>
                    
                        <?php if (isset($view['wr_5']) && $view['wr_5'] == "1") { ?>
                        <a href="<?php echo !empty($mb['mb_hp']) ? 'tel:'.$mb['mb_hp'] : 'javascript:alert(\'등록된 연락처가 없습니다.\')'; ?>" class="view_info_btns_btn1 font-R"><?php echo !empty($mb['mb_hp']) ? $mb['mb_hp'] : '연락처 없음'; ?></a>
                        <?php } else { ?>
                            <?php if(isset($mb['mb_id']) && $mb['mb_id']) { ?> 
                                <a href="<?php echo G5_BBS_URL ?>/memo_form.php?me_recv_mb_id=<?php echo $mb['mb_id'] ?>" onclick="win_memo(this.href); return false;" class="view_info_btns_btn1 font-R">쪽지보내기</a>
                            <?php } ?>
                        <?php } ?>
                    
                    <?php } ?>


                </ul>
                
                <?php if($view['mb_id'] == $member['mb_id'] || $is_admin) { ?>
                <ul class="status_exchange_wrap">
                    <select id="status_exchange" name="wr_status" class="select">
                        <option value="">판매상태 변경</option>
                        <option value="판매중" <?php if (isset($view['wr_8']) && $view['wr_8'] == "판매중") { ?>selected<?php } ?>>판매중</option>
                        <option value="예약중" <?php if (isset($view['wr_8']) && $view['wr_8'] == "예약중") { ?>selected<?php } ?>>예약중</option>
                        <option value="판매완료" <?php if (isset($view['wr_8']) && $view['wr_8'] == "판매완료") { ?>selected<?php } ?>>판매완료</option>
                    </select>
                </ul>
                
                <script>
                    $('#status_exchange').change(function() {
                        statusAjax();
                    });
                    
                    function statusAjax() {
                        var bo_table = "<?php echo isset($bo_table) ? $bo_table : ''; ?>";
                        var wr_id = "<?php echo isset($wr_id) ? $wr_id : ''; ?>";
                        var wr_status = $('select[name="wr_status"]').val();
                        var old_status = "<?php echo isset($view['wr_8']) ? $view['wr_8'] : ''; ?>";
                        
                        if(wr_status) {
                            
                            if(wr_status == old_status) {
                                
                                alert('변경하실 판매 상태와 같습니다.');
                                $('select[name="wr_status"]').val('');
                                return false;
                                
                            } else { 

                                // 사용자에게 확인 창 표시
                                if (confirm('판매 상태를 [' + wr_status + '] 으로 변경하시겠습니까?')) {
                                    // 사용자가 "확인"을 선택한 경우에만 AJAX 요청 실행
                                    $.ajax({
                                        url: '<?php echo $board_skin_url ?>/ajax.status.php',
                                        method: 'POST', // 또는 'GET' 등의 HTTP 메서드
                                        dataType: 'json',
                                        data: {
                                            "bo_table": bo_table,
                                            "wr_id": wr_id,
                                            "wr_status": wr_status,
                                        },
                                        success: function(data) {
                                            if (data.status == 'ok') {
                                                //alert('판매상태를 ' + data.wr_8 + ' 으로 변경 하였습니다.');
                                                location.reload();
                                            } else {
                                                alert('판매상태 변경에 실패 하였습니다.');
                                                $('select[name="wr_status"]').val('');
                                            }
                                        },
                                        error: function(err) {
                                            alert('문제가 발생 했습니다. 다시 시도해주세요.');
                                            $('select[name="wr_status"]').val('');
                                        }
                                    });
                                } else {
                                    $('select[name="wr_status"]').val('');
                                }
                                
                            }
                            
                        } else { 
                            alert('변경하실 판매상태를 선택해주세요.');
                            $('select[name="wr_status"]').val('');
                        }

                    }
            
                </script>
                <?php } ?>
                
                
            </div>
            

            <div class="gap_btm_bd">
                
                <ul class="opt_box_wrap">
                    <li class="font-B">등록일시</li>
                    <li><?php echo $view['wr_datetime']; ?></li>
                </ul>
                
                <ul class="opt_box_wrap">
                    <li class="font-B">판매상태</li>
                    <li class="font-B"><?php echo !empty($view['wr_8']) ? $view['wr_8'] : '판매중'; ?></li>
                </ul>

                <ul class="opt_box_wrap">
                    <li class="font-B">거래옵션</li>
                    <li><?php echo !empty($view['wr_1']) ? $view['wr_1'] : '배송불가'; ?> <?php echo !empty($view['wr_2']) ? '('.$view['wr_2'].')' : '(직거래 불가)'; ?></li>
                </ul>
                
                <ul class="opt_box_wrap">
                    <li class="font-B">상품상태</li>
                    <li><?php echo !empty($view['wr_4']) ? $view['wr_4'] : '정보없음'; ?></li>
                </ul>
                
                

            </div>

            
            <?php 
            if(isset($board['bo_use_signature']) && $board['bo_use_signature']) {
                // 서명 출력
                include_once(G5_PATH.'/rb/rb.mod/signature/signature.skin.php');
            } 
            ?>
               
            <?php if(isset($mb['mb_id']) && $mb['mb_id']) { ?> 
            
            <div class="seller_info_wrap">
                <span class="seller_info_wrap_tit font-B"><?php echo !empty($mb['mb_nick']) ? $mb['mb_nick'] : 'Guest'; ?>님의 상품</span>
                
                <?php
                    $tmp_write_table = $g5['write_prefix'].$bo_table;
                    
                    $sqls = " select * from {$tmp_write_table} where wr_id != '{$wr_id}' and mb_id = '{$mb['mb_id']}' and wr_is_comment = '0' and wr_option NOT IN ('secret') order by wr_id desc limit 6 ";
                    $results = sql_query($sqls);
                
                    $thumb_widths = 120;
                    $thumb_heights = 120;
                ?>

                
                <!-- { -->
                <ul class="bbs_main_wrap_thumb_con">
                    <div class="swiper-container swiper-container-deal">
                        <ul class="swiper-wrapper swiper-wrapper-deal">
                
                <?php 
                for ($i=0; $rows=sql_fetch_array($results); $i++) { 

                    $hrefs = get_pretty_url($bo_table, $rows['wr_id']);
                    
                    //썸네일
                    $thumbs = get_list_thumbnail($bo_table, $rows['wr_id'], $thumb_widths, $thumb_heights, false, true);
                    
                    if($thumbs['src']) {
                        $imgs = $thumbs['src'];
                    } else {
                        $imgs = G5_THEME_URL.'/rb.img/no_image.png';
                    }
                    
                    //썸네일 출력 class="skin_list_image" 필수 (높이값 설정용)
                    $img_contents = '<img src="'.$imgs.'" class="skin_list_image">';

                ?>


                            <dd class="swiper-slide swiper-slide-deal" onclick="location.href='<?php echo $hrefs ?>';">
                                
                                <div>
                                    
                                    <?php if($thumbs['src']) { ?>
                                    <ul class="bbs_main_wrap_con_ul1">
                                        <a href="<?php echo $hrefs ?>"><?php echo run_replace('thumb_image_tag', $img_contents, $thumbs); ?></a>
                                    </ul>
                                    <?php } ?>

                                    
                                    <ul class="bbs_main_wrap_con_ul2" <?php if(!$thumbs['src']) { ?>style="padding-right:0px;"<?php } ?>>
                                        <li class="bbs_main_wrap_con_subj cut"><a href="<?php echo $hrefs ?>" class="font-B"><?php echo $rows['wr_subject'] ?></a></li>
                                        


                                            <li class="bbs_main_wrap_con_info">
                                                <?php if($rows['ca_name']) { ?>
                                                <?php echo $rows['ca_name'] ?><br>
                                                <?php } ?>
                                                <?php echo passing_time($rows['wr_datetime']) ?>
                                            </li>

                                    </ul>
                                    <div class="cb"></div>
                                </div>
                            </dd>
                            <!-- } -->
                            
                            <?php }  ?>
                            <?php if ($i == 0) { //게시물이 없을 때  ?>
                            <dd class="no_data" style="width:100% !important;">등록한 상품이 없습니다.</dd>
                            <?php }  ?>
                            

                            
                        </ul>
                    </div>
                    
                    <!-- 모듈세팅 { -->
                    <script>
                                                
                        var swiper = new Swiper('.swiper-container-deal', {
                            slidesPerColumnFill: 'row', //세로형
                            slidesPerView: 3, //가로갯수
                            slidesPerColumn: 999, // 세로갯수
                            spaceBetween: 10, // 간격
                            observer: true, //리셋
                            observeParents: true, //리셋
                            touchRatio: 0, // 드래그 가능여부
                        });
                        

                    </script>
                    <!-- } -->
                    
                </ul>
                <!-- } -->
                
                <button type="button" onclick="location.href='<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>&sop=and&sfl=mb_id&stx=<?php echo $mb['mb_id'] ?>';" class="more_all_btn font-B">상품 전체보기</button>
                
            </div>
            <?php } ?>
                
                <!-- 첨부파일 / 링크 { -->
                <?php
                $cnt = 0;
                if ($view['file']['count']) {
                    for ($i=0; $i<count($view['file']); $i++) {
                        if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                        //if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'])
                            $cnt++;
                    }
                }

                ?>

                <?php if($cnt) { ?>
                
                <div class="down_link_wrap">
                <h2 id="container_title" class="none_btm_pd font-R">다운로드</h2>

                    <div class="rb_bbs_file">
                        <?php
                        // 가변 파일
                        for ($i=0; $i<count($view['file']); $i++) {
                            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
                            //if (isset($view['file'][$i]['source']) && $view['file'][$i]['source']) {
                        ?>
                        <ul class="rb_bbs_file_for view_file_download" onclick="location.href='<?php echo $view['file'][$i]['href']; ?>';">
                            <i><img src="<?php echo $board_skin_url ?>/img/ico_file.svg"></i>
                            <a href="javascript:void(0);"><?php echo $view['file'][$i]['source'] ?></a> <?php echo number_format($view['file'][$i]['download']); ?>회
                            <?php if($view['file'][$i]['content']) { ?>
                            <li class="file_contents"><?php echo $view['file'][$i]['content'] ?></li>
                            <?php } ?>
                        </ul>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    
                </div>
                <?php } ?>



                <?php if(isset($view['link']) && array_filter($view['link'])) { ?>
                <div class="down_link_wrap">
                <h2 id="container_title" class="none_btm_pd font-R">링크</h2>
                    <div class="rb_bbs_file">
                        <?php
                        // 링크
                        $cnt = 0;
                        for ($i=1; $i<=count($view['link']); $i++) {
                            if ($view['link'][$i]) {
                                $cnt++;
                                $link = cut_str($view['link'][$i], 70);
                        ?>
                        <ul class="rb_bbs_file_for" onclick="window.open('<?php echo $view['link_href'][$i]; ?>');">
                            <i><img src="<?php echo $board_skin_url ?>/img/ico_link.svg"></i>
                            <a href="javascript:void(0);"><?php echo $link ?></a>　<?php echo $view['link_hit'][$i] ?>회
                        </ul>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>
                
            
            
            
            
        </ul>
        
        
        
        
        <ul class="bbs_sv_wrap_ul1">
            <div class="po_rel">
                <?php 
                $v_img_count = count($view['file']);
                if ($v_img_count > 0) {
                    echo "<div class=\"swiper-container swiper-container-pf\"><ul class=\"swiper-wrapper swiper-wrapper-pf\">\n";
                    for ($i = 0; $i < $v_img_count; $i++) {
                        // Check if $view['file'][$i]['view'] exists and is not empty
                        if (isset($view['file'][$i]['view']) && $view['file'][$i]['view']) {
                            echo "<li class=\"swiper-slide swiper-slide-pf\">" . get_view_thumbnail($view['file'][$i]['view']) . "</li>";
                        }
                    }
                    echo "</ul><div class=\"swiper-pagination swiper-pagination-pf\"></div></div>\n";
                ?>

                <script>
                    var swiper = new Swiper('.swiper-container-pf', {
                        slidesPerView: 1, //가로갯수
                        spaceBetween: 0, // 간격
                        observer: true, //리셋
                        observeParents: true, //리셋
                        touchRatio: 1, // 드래그 가능여부
                        autoHeight: true,

                        autoplay: { //오토플레이
                            delay: 3000, //시간
                            disableOnInteraction: false, //마우스온여부
                        },

                        pagination: {
                            el: '.swiper-pagination-pf',
                            dynamicBullets: true,
                            clickable: true,
                        }

                    });
                </script>

                    <?php
                    }

                ?>
                
                <?php if(isset($view['wr_8']) && $view['wr_8'] == "예약중") { ?>
                <span class="po_rel_blank">
                    <dd class="font-R">
                    <img src="<?php echo $board_skin_url ?>/img/deal_ico_y.svg"><br><br>
                    이미 다른분과 거래예약이 된<br>
                    상품입니다.
                    </dd>
                </span>
                <?php } else if(isset($view['wr_8']) && $view['wr_8'] == "판매완료") { ?>
                <span class="po_rel_blank">
                    <dd class="font-R">
                    판매 완료된 상품입니다.
                    </dd>
                </span>
                <?php } ?>
                
            </div>
            

            <!-- 본문 내용 시작 { -->
            <div id="bo_v_con">
                <h2 id="container_title" class="mo_pd_none">상세정보</h2>
                <?php echo get_view_thumbnail($view['content']); ?>
            </div>
            
            
            <?php 
                if(isset($wr_3[0]) && $wr_3[0]) {
                if(isset($config['cf_kakao_js_apikey']) && $config['cf_kakao_js_apikey']) { 

                    //기본좌표
                    if (!isset($wr_3[4])) {
                        $wr_3[4] = 37.566400714093284;
                    }
                    if (!isset($wr_3[5])) {
                        $wr_3[5] = 126.9785391897507;
                    }


                ?>
                <h2 id="container_title" class="mo_pd_none">직거래 위치</h2>

                <div class="rc_wrap3">
                            <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?php echo $config['cf_kakao_js_apikey'] ?>&libraries=services"></script>
                            <div style="background-color:#f9f9f9; width:100%; height:200px; border-radius:10px;" id="map"></div>

                            <script>
                            var mapContainer = document.getElementById('map'), // 지도를 표시할 div 
                                mapOption = {
                                    center: new daum.maps.LatLng(<?php echo $wr_3[4] ?>, <?php echo $wr_3[5] ?>), // 지도의 중심좌표
                                    level: 3 // 지도의 확대 레벨
                                };

                            // 지도를 생성
                            var map = new daum.maps.Map(mapContainer, mapOption);

                            // 일반 지도와 스카이뷰로 지도 타입을 전환할 수 있는 지도타입 컨트롤을 생성합니다
                            var mapTypeControl = new daum.maps.MapTypeControl();

                            // 지도에 컨트롤을 추가해야 지도위에 표시됩니다
                            // daum.maps.ControlPosition은 컨트롤이 표시될 위치를 정의하는데 TOPRIGHT는 오른쪽 위를 의미합니다
                            map.addControl(mapTypeControl, daum.maps.ControlPosition.TOPRIGHT);

                            // 지도 확대 축소를 제어할 수 있는  줌 컨트롤을 생성합니다
                            var zoomControl = new daum.maps.ZoomControl();
                            map.addControl(zoomControl, daum.maps.ControlPosition.RIGHT);

                            // 주소-좌표 변환 객체 생성
                            var geocoder = new daum.maps.services.Geocoder();

                            // 마커
                            var marker = new daum.maps.Marker({
                                map: map,
                                // 지도 중심좌표에 마커를 생성
                                position: map.getCenter()
                            });

                            //마커를 기준으로 가운데 정렬이 될 수 있도록 추가
                            var markerPosition = marker.getPosition(); 
                            map.relayout();
                            map.setCenter(markerPosition);



                            //브라우저가 리사이즈될때 지도 리로드 //아이폰 이슈
                            /*
                            $(window).on('resize', function () {
                                var markerPosition = marker.getPosition(); 
                                map.relayout();
                                map.setCenter(markerPosition)
                            });
                            */


                            </script>
                            <div class="flex_gbtns">
                            <span class="rc_sub_title2 font-R"><?php echo isset($wr_3[0]) ? $wr_3[0] : ''; ?> <?php echo isset($wr_3[1]) ? $wr_3[1] : ''; ?></span>
                            <a href="https://map.kakao.com/?q=<?php echo isset($wr_3[4]) ? $wr_3[4] : ''; ?> <?php echo isset($wr_3[5]) ? $wr_3[5] : ''; ?>" target="_blank">길찾기</a>
                            </div>
                </div>
                <?php } ?>
                <?php } ?>
            
            

            <div class="at_cont">
                <?php echo isset($config['cf_title']) ? $config['cf_title'] : ''; ?>은(는) 통신판매의 당사자가 아닙니다. 전자상거래 등에서의 소비자보호에 관한 법률 등 관련 법령 및 <?php echo isset($config['cf_title']) ? $config['cf_title'] : ''; ?>의 약관에 따라 상품, 상품정보, 거래에 관한 책임은 개별 판매자에게 귀속하고, <?php echo isset($config['cf_title']) ? $config['cf_title'] : ''; ?>은(는) 원칙적으로 회원간 거래에 대하여 책임을 지지 않습니다.
            </div>

            
            
            <div class="mt-40">
                
                
                <!-- 게시물 정보 { -->
                <ul class="rb_bbs_for_mem rb_bbs_for_mem_view">

                    <li class="fl">
                        <dd><?php echo $view['name'] ?></dd>
                    </li>
                    <li class="rb_bbs_for_btm_info">
                       
                        
                        <dd><span><?php echo passing_time3($view['wr_datetime']) ?></span></dd>
                        <dd>
                            <i><img src="<?php echo $board_skin_url ?>/img/ico_eye.svg"></i>
                            <span><?php echo number_format($view['wr_hit']); ?></span>
                        </dd>

                        <dd>
                            <i><img src="<?php echo $board_skin_url ?>/img/ico_comm.svg"></i>
                            <span><?php echo number_format($view['wr_comment']); ?></span>
                        </dd>

                    </li>

                    <div class="cb"></div>

                </ul>
                <!-- } -->




                <ul class="btm_btns">


                    <dd class="btm_btns_right">

                        <?php if ($list_href) { ?>
                        <a href="<?php echo $list_href ?>" type="button" class="fl_btns font-B">목록</a>
                        <?php } ?>


                        <?php if ($scrap_href) { ?>
                        <a href="<?php echo $scrap_href;  ?>" class="fl_btns font-B" target="_blank" onclick="win_scrap(this.href); return false;">스크랩</a>
                        <?php } ?>

                        <?php if ($write_href) { ?>
                        <button type="button" name="btn_submit" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
                            <img src="<?php echo $board_skin_url ?>/img/ico_write.svg">
                            <span class="font-R">글 등록</span>
                        </button>
                        <?php } ?>

                        <div class="cb"></div>

                    </dd>

                    <div id="bo_v_btns">
                        <?php ob_start(); ?>

                        <?php if($update_href || $delete_href || $copy_href || $move_href || $search_href) { ?>

                            <?php if ($reply_href) { ?>
                            <!-- // 답글을 사용하지 않음
                            <a href="<?php echo $reply_href ?>" class="fl_btns">
                            <span class="font-B">답글</span>
                            </a>
                            -->
                            <?php } ?>

                            <?php if ($update_href) { ?>
                            <a href="<?php echo $update_href ?>" class="fl_btns">
                            <span class="font-B">수정</span>
                            </a>
                            <?php } ?>

                            <?php if ($copy_href) { ?>
                            <a href="<?php echo $copy_href ?>" onclick="board_move(this.href); return false;" class="fl_btns">
                            <span class="font-B">복사</span>
                            </a>
                            <?php } ?>

                            <?php if ($move_href) { ?>
                            <a href="<?php echo $move_href ?>" onclick="board_move(this.href); return false;" class="fl_btns">
                            <span class="font-B">이동</span>
                            </a>
                            <?php } ?>

                            <?php if ($delete_href) { ?>
                            <a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;" class="fl_btns">
                            <span class="font-B">삭제</span>
                            </a>
                            <?php } ?>

                        <?php } ?>

                        <?php
                        $link_buttons = ob_get_contents();
                        ob_end_flush();
                       ?>

                    </div>


                    <dd class="cb"></dd>


                </ul>

                <!-- 배너 {
                <ul class="bbs_bn_box">
                    배너를 추가해보세요.
                </ul>
                } -->

                

                <ul>
                    <?php if ($prev_href || $next_href) { ?>
                    <div class="bo_v_nb">
                        <?php if ($prev_href) { ?><li class="btn_prv" onclick="location.href='<?php echo $prev_href ?>';"><span class="nb_tit">이전글</span><a href="javascript:void(0);"><?php echo $prev_wr_subject;?></a><span class="nb_date"><?php echo str_replace('-', '.', substr($prev_wr_date, '0', '10')); ?></span></li><?php } ?>
                        <?php if ($next_href) { ?><li class="btn_next" onclick="location.href='<?php echo $next_href ?>';"><span class="nb_tit">다음글</span><a href="javascript:void(0);"><?php echo $next_wr_subject;?></a><span class="nb_date"><?php echo str_replace('-', '.', substr($next_wr_date, '0', '10')); ?></span></li><?php } ?>
                    </div>
                    <?php } ?>

                    <?php
                    // 코멘트 입출력
                    include_once(G5_BBS_PATH.'/view_comment.php');
                    ?>
                </ul>
                
                
            </div>
        </ul>
        
        <div class="cb"></div>
    </div>
    
    
    
    

</div>













<script>
<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $("a.view_file_download").click(function() {
        if(!g5_is_member) {
            alert("다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.");
            return false;
        }

        var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

        if(confirm(msg)) {
            var href = $(this).attr("href")+"&js=on";
            $(this).attr("href", href);

            return true;
        } else {
            return false;
        }
    });
});
<?php } ?>

function board_move(href)
{
    window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // 추천, 비추천
    $("#good_button, #nogood_button").click(function() {
        var $tx;
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();
});

function excute_good(href, $el, $tx)
{
    $.post(
        href,
        { js: "on" },
        function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("nogood") > -1) {
                    $tx.text("이 글을 비추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                } else {
                    $tx.text("이 글을 추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                }
            }
        }, "json"
    );
}
</script>
<!-- } 게시글 읽기 끝 -->