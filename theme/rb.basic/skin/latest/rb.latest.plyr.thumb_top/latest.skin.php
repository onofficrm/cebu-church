<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$rb_skin = sql_fetch (" select * from {$rb_module_table} where md_id = '{$options}' "); //최신글 환경설정 테이블 조회 (삭제금지)

$thumb_width = 400;
$thumb_height = 333;
$list_count = (is_array($list) && $list) ? count($list) : 0;

//모듈 타이틀이 설정되지 않은 경우 게시판 제목을 보여줍니다.
if($rb_skin['md_title']) {
    $bo_subject = $rb_skin['md_title'];
} else {
    $bo_subject = $rb_skin['md_title'];
}

//카테고리 출력옵션을 사용한 경우 카테고리 링크로 이동합니다.
if($rb_skin['md_sca']) {
    $links_url = get_pretty_url($bo_table,'','sca='.urlencode($rb_skin['md_sca']));
} else {
    $links_url = get_pretty_url($bo_table);
}

/*
모듈설정 연동 변수
$rb_skin['md_id'] 설정ID
$rb_skin['md_layout'] 레이아웃 섹션ID
$rb_skin['md_layout_name'] 레이아웃 스킨명
$rb_skin['md_theme'] 테마명
$rb_skin['md_title'] 타이틀(제목)
$rb_skin['md_bo_table'] 게시판ID
$rb_skin['md_skin'] 스킨명
$rb_skin['md_cnt'] 출력갯수
$rb_skin['md_col'] 행갯수
$rb_skin['md_row'] 열갯수
$rb_skin['md_col_mo'] 행갯수(모바일)
$rb_skin['md_row_mo'] 열갯수(모바일)
$rb_skin['md_gap'] 게시물 간격(여백)
$rb_skin['md_gap_mo'] 모바일 게시물 간격(여백)
$rb_skin['md_width'] 가로사이즈
$rb_skin['md_height'] 세로사이즈
$rb_skin['md_auto_time'] 자동롤링 시간
$rb_skin['md_thumb_is'] 썸네일 출력여부(1,0)
$rb_skin['md_nick_is'] 닉네임 출력여부(1,0)
$rb_skin['md_date_is'] 작성일 출력여부(1,0)
$rb_skin['md_content_is'] 본문내용 출력여부(1,0)
$rb_skin['md_icon_is'] 아이콘 출력여부(1,0)
$rb_skin['md_comment_is'] 댓글수 출력여부(1,0)
$rb_skin['md_swiper_is'] 스와이프 여부(1,0)
$rb_skin['md_auto_is'] 자동롤링 여부(1,0)
*/

?>

<link rel="stylesheet" href="<?php echo $latest_skin_url ?>/style.css?ver=<?php echo G5_TIME_YMDHIS ?>">



            <div class="bbs_main">

                <!-- { -->
                <ul class="bbs_main_wrap_tit">

                    <li class="bbs_main_wrap_tit_l">
                        <!-- 타이틀 { -->
                        <a href="<?php echo $links_url; ?>"><h2 class="font-B"><?php echo $bo_subject ?></h2></a>
                        <!-- } -->
                    </li>


                    <li class="bbs_main_wrap_tit_r">

                        <?php if($rb_skin['md_swiper_is'] == 1) { //모듈설정:스와이프 사용여부(1,0)?>
                        <!-- 좌우 페이징 { -->
                        <button type="button" class="arr_prev_btn arr_sw_prev1 swiper-button-prev arr_sw_prev_<?php echo $rb_skin['md_id'] ?>">
                            <img src="<?php echo G5_THEME_URL ?>/rb.img/icon/arr_prev.svg">
                        </button>
                        <button type="button" class="arr_next_btn arr_sw_next1 swiper-button-next arr_sw_next_<?php echo $rb_skin['md_id'] ?>">
                            <img src="<?php echo G5_THEME_URL ?>/rb.img/icon/arr_next.svg">
                        </button>
                        <!-- } -->
                        <?php } ?>

                        <button type="button" class="more_btn" onclick="location.href='<?php echo $links_url; ?>';">더보기</button>

                    </li>

                    <div class="cb"></div>
                </ul>
                <!-- } -->

                <!-- { -->
                <ul class="bbs_main_wrap_plyr_thumb_top_con">

                    <!-- swiper-container-모듈아이디 -->
                    <div class="swiper-container swiper-container-<?php echo $rb_skin['md_id'] ?>">

                        <!-- swiper-wrapper-모듈아이디 -->
                        <ul class="swiper-wrapper swiper-wrapper-<?php echo $rb_skin['md_id'] ?>">

                            <?php
                                for ($i=0; $i<$list_count; $i++) {

                                //썸네일
                                $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);

                                //썸네일여부 확인
                                if($thumb['src']) {
                                    if (strstr($list[$i]['wr_option'], 'secret')) {
                                        $img = G5_THEME_URL.'/rb.img/sec_image.png';
                                    } else {
                                        $img = $thumb['src'];
                                    }
                                } else {
                                    $img = G5_THEME_URL.'/rb.img/no_image.png';
                                    $thumb['alt'] = '이미지가 없습니다.';
                                }

                                //썸네일 출력 class="skin_list_image" 필수 (높이값 설정용)
                                $img_content = '<img src="'.$img.'" alt="'.$thumb['alt'].'" class="skin_list_image">';

                                //게시물 링크
                                $wr_href = get_pretty_url($bo_table, $list[$i]['wr_id']);

                                $sec_txt = '<span style="opacity:0.6">작성자 및 관리자 외 열람할 수 없습니다.<br>비밀글 기능으로 보호된 글입니다.</span>';

                                //본문출력 (class="cut" : 한줄자르기 / class="cut2" : 두줄자르기)
                                //$wr_content = preg_replace("/<(.*?)\>/","",$list[$i]['wr_content']);
                                //$wr_content = preg_replace("/&nbsp;/","",$wr_content);
                                //$wr_content = preg_replace("/&gt;/","",$wr_content);
                                $wr_content = strip_tags($list[$i]['wr_content']);

								$is_vimg = false;

								// 링크 유튜브,비메오 ID 추출
								if($list[$i]['wr_link1']) {
									$vimg = get_video_info(trim(strip_tags($list[$i]['wr_link1'])));
									if(in_array($vimg['provider'], array("youtube", "vimeo"))){
										$is_vimg = true;
									}
								}
                            ?>


                            <!-- for { -->
                            <!-- swiper-slide-모듈아이디 -->
                            <dd class="swiper-slide swiper-slide-<?php echo $rb_skin['md_id'] ?> is_hover">

                                <div>

                                    <?php if($rb_skin['md_thumb_is'] == 1) { //모듈설정:썸네일 출력여부(1,0)?>
                                    <ul class="bbs_main_wrap_con_ul1">
										<?php if($is_vimg){ ?>
										<div style="position:relative;overflow:hidden;border-radius:10px;">
											<div class="vidiom_player" data-plyr-provider="<?php echo $vimg['provider'];?>" data-plyr-embed-id="<?php echo $vimg['video_id'];?>"></div>
										</div>
										<?php }else{ ?>
                                        <a href="<?php echo $wr_href ?>"><?php echo run_replace('thumb_image_tag', $img_content, $thumb); ?></a>
										<?php } ?>

                                        <?php if($rb_skin['md_icon_is'] == 1) { //모듈설정:아이콘 출력여부(1,0)?>
                                            <div class="icon_abs">
                                            <?php if ($list[$i]['icon_new']) echo "<span class=\"bbs_list_label label3\">새글</span>"; ?>
                                            <?php if ($list[$i]['icon_hot']) echo "<span class=\"bbs_list_label label1\">인기</span>"; ?>
                                            </div>
                                        <?php } ?>
                                    </ul>
                                    <?php } ?>

                                    <ul class="bbs_main_wrap_con_ul2" <?php if($rb_skin['md_thumb_is'] != 1) { //모듈설정:썸네일 출력하지 않는경우 ?>style="width:100%"<?php } ?>>

                                        <?php if($rb_skin['md_subject_is'] == 1) { //모듈설정:제목 출력여부(1,0) ?>
                                        <li class="bbs_main_wrap_con_subj cut"><a href="<?php echo $wr_href ?>" class="font-B"><?php echo $list[$i]['subject'] ?></a></li>
                                        <?php } ?>

                                        <?php if($rb_skin['md_content_is'] == 1) { //모듈설정:본문 출력여부(1,0)?>

                                           <?php if (strstr($list[$i]['wr_option'], 'secret')) { ?>
                                                <li class="bbs_main_wrap_con_cont">
                                                    <?php echo $sec_txt; ?>
                                                </li>
                                            <?php } else { ?>
                                                <li class="bbs_main_wrap_con_cont cut2">
                                                    <a href="<?php echo $wr_href ?>"><?php echo $wr_content; ?></a>
                                                </li>
                                            <?php } ?>

                                        <?php } ?>

                                        <?php if($rb_skin['md_nick_is'] == 1 || $rb_skin['md_date_is'] == 1 || $rb_skin['md_ca_is'] == 1 || $rb_skin['md_comment_is'] == 1) {?>
                                            <li class="bbs_main_wrap_con_info">

                                                <?php if($rb_skin['md_nick_is'] == 1) { //모듈설정:작성자 출력여부(1,0)?>
                                                <span class="font-B"><?php echo $list[$i]['wr_name'] ?></span>
                                                <?php } ?>

                                                <?php if($rb_skin['md_date_is'] == 1) { //모듈설정:작성일 출력여부(1,0)?>
                                                <?php echo passing_time($list[$i]['wr_datetime']) ?>
                                                <?php } ?>

                                                <?php if($rb_skin['md_ca_is'] == 1 && $list[$i]['ca_name']) { //모듈설정:카테고리 출력여부(1,0) || 카테고리 있을때만?>
                                                <?php echo $list[$i]['ca_name'] ?>
                                                <?php } ?>

                                                <?php if($rb_skin['md_comment_is'] == 1) { //모듈설정:댓글 출력여부(1,0 || 댓글이 0개 이상인 경우)?>
                                                    <?php if($list[$i]['comment_cnt']) { ?>
                                                        댓글 <?php echo number_format($list[$i]['wr_comment']); ?>
                                                    <?php } ?>
                                                    조회 <?php echo number_format($list[$i]['wr_hit']); ?>
                                                <?php } ?>

                                            </li>
                                        <?php } ?>


                                    </ul>
                                    <div class="cb"></div>
                                </div>
                            </dd>
                            <!-- } -->

                            <?php }  ?>
                            <?php if ($list_count == 0) { //게시물이 없을 때  ?>
                            <dd class="no_data" style="width:100% !important;">데이터가 없습니다.</dd>
                            <?php }  ?>



                        </ul>
                    </div>

                    <!-- 모듈세팅 { -->
                    <script>

                        var swiper = new Swiper('.swiper-container-<?php echo $rb_skin['md_id'] ?>', {
                            slidesPerColumnFill: 'row', //세로형
                            slidesPerView: <?php echo $rb_skin['md_col'] ?>, //가로갯수
                            slidesPerColumn: <?php echo $rb_skin['md_row'] ?>, // 세로갯수
                            spaceBetween: <?php echo $rb_skin['md_gap'] ?>, // 간격
                            observer: true, //리셋
                            observeParents: true, //리셋
                            touchRatio: <?php echo $rb_skin['md_swiper_is'] ?>, // 드래그 가능여부
                            slidesPerGroup:<?php echo $rb_skin['md_col'] ?>,
                            //loop: true, //반복

                            <?php if($rb_skin['md_auto_is'] == 1) { //모듈설정:자동롤링여부(1,0)?>

                            autoplay: { //오토플레이
                                delay: <?php echo $rb_skin['md_auto_time'] //모듈설정:자동롤링시간?>, //시간
                                disableOnInteraction: false,
                            },

                            <?php } ?>

                            /*
                            pagination: {
                                el: '.swiper-pagination',
                                dynamicBullets: true,
                                clickable: true,
                            },
                            */

                            <?php if($rb_skin['md_swiper_is'] == 1) { //모듈설정:스와이프 사용여부(1,0)?>
                            //arr_sw_next_모듈아이디 / arr_sw_prev_모듈아이디
                            navigation: {
                                nextEl: '.arr_sw_next_<?php echo $rb_skin['md_id'] ?>',
                                prevEl: '.arr_sw_prev_<?php echo $rb_skin['md_id'] ?>',
                            },
                            <?php } ?>

                            breakpoints: { //반응형세팅
                                1024: {
                                    slidesPerView: <?php echo $rb_skin['md_col'] ?>, //가로갯수
                                    slidesPerColumn: <?php echo $rb_skin['md_row'] ?>, //세로갯수
                                    spaceBetween: <?php echo $rb_skin['md_gap'] ?>, //간격
                                    slidesPerGroup:<?php echo $rb_skin['md_col'] ?>,
                                },
                                10: {
                                    slidesPerView: <?php echo $rb_skin['md_col_mo'] ?>, //모바일 가로갯수
                                    slidesPerColumn: <?php echo $rb_skin['md_row_mo'] ?>, //모바일 세로갯수
                                    spaceBetween: <?php echo $rb_skin['md_gap_mo'] ?>, //모바일 간격
                                    slidesPerGroup:<?php echo $rb_skin['md_col_mo'] ?>,
                                }
                            }

                        });


                    </script>
                    <!-- } -->

                    <!-- Plyr { -->
					<script>
					// Initilize player js
					if (typeof Plyr != "undefined") {
						const vidiom_player = Plyr.setup( '.vidiom_player', {
							controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'fullscreen'], // 'settings'
							disableContextMenu: true,
							quality: true
						});
					}
					</script>
                    <!-- } -->


                </ul>
                <!-- } -->

            </div>

