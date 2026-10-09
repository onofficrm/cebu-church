<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 방지

include_once(G5_THEME_PATH.'/head.php');

// --- [PHP 로직: 유튜브 헬퍼 함수] ---
function get_yt_id($url) {
    if (!$url) return '';
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) return $url;
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $match)) return $match[1];
    return '';
}

function get_yt_thumb($id) {
    return $id ? "https://img.youtube.com/vi/{$id}/maxresdefault.jpg" : "https://placehold.co/1280x720/1e293b/ffffff?text=Cebu+Church";
}

// --- [아이콘 정의] ---
$icons = [
    'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>',
    'play' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="m5 3 14 9-14 9V3z"/></svg>',
    'chevron-right' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
    'youtube' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>',
    'map-pin' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>'
];
?>

<!-- Tailwind CSS & 전용 스타일 -->
<script src="https://cdn.tailwindcss.com"></script>
<style>
    @import url("https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css");
    #home-container { font-family: 'Pretendard', sans-serif; color: #1a1a1a; }
    .keep-all { word-break: keep-all; }
    .modern-arch { border-radius: 120px 120px 24px 24px; transition: all 0.5s ease; }
    .staff-card:hover .modern-arch { transform: translateY(-10px); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1); }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-up { animation: fadeUp 0.8s ease-out forwards; }
    .glass-hero { background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    .glass-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.1); }
</style>

<div id="home-container" class="w-full bg-white">
    
    <!-- [Hero Section] - 높이 축소 및 비전 추가 -->
    <section class="relative min-h-[70vh] md:h-[80vh] flex items-center justify-center overflow-hidden py-12">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1438232992991-995b7058bbb3?q=80&w=2000" class="w-full h-full object-cover" alt="Church Interior">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/80 via-slate-950/40 to-slate-950/90"></div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-6 w-full flex flex-col items-center text-center">
            <div class="animate-fade-up">
                <span class="bg-indigo-600 text-white px-4 py-1 rounded-full text-[10px] md:text-xs font-bold tracking-[0.3em] mb-6 border border-white/20 inline-block uppercase">2026 MINISTRY THEME</span>
                <h1 class="text-3xl md:text-7xl font-black text-white leading-tight mb-4">한 영혼을 <span class="text-rose-500">주</span>께로!<br>한 가정을 <span class="text-rose-500">주</span>께로!</h1>
                
                <div class="max-w-xl glass-hero p-6 md:p-8 rounded-[2rem] mt-6 mx-auto">
                    <p class="text-base md:text-lg font-light text-indigo-50 italic">"주 예수를 믿으라 그리하면 너와 네 집이 구원을 받으리라"</p>
                    <div class="mt-3 text-[9px] text-white/30 tracking-widest uppercase italic">Acts 16:31</div>
                </div>
            </div>

            <!-- [4대 비전 리스트] -->
            <div class="w-full max-w-5xl grid grid-cols-2 md:grid-cols-4 gap-3 mt-12 animate-fade-up" style="animation-delay: 0.2s;">
                <?php 
                $visions = [
                    ['id' => '01', 'title' => '신앙계승', 'sub' => '3代 Heritage'],
                    ['id' => '02', 'title' => '한인복음화', 'sub' => 'Korean Mission'],
                    ['id' => '03', 'title' => '필리핀복음화', 'sub' => 'Local Outreach'],
                    ['id' => '04', 'title' => '동남아복음화', 'sub' => 'Global Vision'],
                ];
                foreach($visions as $v): ?>
                <div class="glass-card p-4 md:p-6 rounded-2xl transition-all duration-500 hover:bg-white/10 hover:-translate-y-1 group border border-white/5">
                    <span class="text-[9px] font-black text-indigo-400 mb-1.5 block tracking-widest uppercase"><?php echo $v['id'] ?></span>
                    <h4 class="text-base md:text-lg font-bold text-white mb-0.5 group-hover:text-rose-400 transition-colors"><?php echo $v['title'] ?></h4>
                    <p class="text-[8px] md:text-[9px] font-medium text-white/20 tracking-widest uppercase"><?php echo $v['sub'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 md:px-6 py-24 space-y-40">
        
        <!-- [1. 교회소식 & 카카오톡] -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2">
                <div class="flex justify-between items-end mb-8 border-b border-gray-100 pb-4">
                    <h3 class="text-3xl font-bold text-slate-900 tracking-tight">교회소식</h3>
                    <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=notice" class="text-sm font-bold text-blue-600 flex items-center gap-1">모두보기 <?php echo $icons['chevron-right'] ?></a>
                </div>
                <div class="space-y-4">
                    <?php
                    $sql = " select * from {$g5['write_prefix']}notice where wr_is_comment = 0 order by wr_num limit 3 ";
                    $result = sql_query($sql);
                    while ($row = sql_fetch_array($result)) {
                        $link = G5_BBS_URL.'/board.php?bo_table=notice&wr_id='.$row['wr_id'];
                    ?>
                    <a href="<?php echo $link ?>" class="group flex items-center justify-between p-6 bg-slate-50 rounded-[1.5rem] hover:bg-white hover:shadow-xl transition-all border border-transparent hover:border-blue-100">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 bg-white text-blue-600 rounded-xl flex items-center justify-center shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-colors"><?php echo $icons['calendar'] ?></div>
                            <h4 class="font-bold text-lg text-slate-800 line-clamp-1"><?php echo get_text($row['wr_subject']) ?></h4>
                        </div>
                        <span class="text-slate-400 text-sm"><?php echo date("Y.m.d", strtotime($row['wr_datetime'])) ?></span>
                    </a>
                    <?php } ?>
                </div>
            </div>
            
            <div class="bg-[#FAE100] rounded-[2.5rem] p-8 flex flex-col justify-between shadow-xl shadow-yellow-500/10 min-h-[300px]">
                <div>
                    <div class="w-12 h-12 bg-[#371D1E] rounded-xl flex items-center justify-center text-[#FAE100] mb-6 font-black italic">K</div>
                    <h3 class="text-2xl font-black text-[#371D1E] leading-tight">우리 친구할까요?<br>카톡 채널추가</h3>
                    <p class="text-[#371D1E]/60 text-sm mt-2 font-bold">@세부한인교회 플러스친구</p>
                </div>
                <a href="#" class="w-full bg-[#371D1E] text-white py-4 rounded-xl font-bold flex items-center justify-center gap-2">채널 바로가기 <?php echo $icons['chevron-right'] ?></a>
            </div>
        </section>

        <!-- [2. 갤러리] -->
        <section>
            <div class="flex justify-between items-end mb-12">
                <h3 class="text-3xl font-bold text-slate-900 tracking-tight">갤러리</h3>
                <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=gallery" class="text-sm font-bold text-blue-600 flex items-center gap-1">사진 더보기 <?php echo $icons['chevron-right'] ?></a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php
                $sql = " select * from {$g5['write_prefix']}gallery where wr_is_comment = 0 order by wr_num limit 4 ";
                $result = sql_query($sql);
                while ($row = sql_fetch_array($result)) {
                    $thumb = get_list_thumbnail('gallery', $row['wr_id'], 400, 500, false, true);
                    $img_src = (isset($thumb['src']) && $thumb['src']) ? $thumb['src'] : "https://placehold.co/400x500/f1f5f9/94a3b8?text=No+Image";
                ?>
                <a href="<?php echo G5_BBS_URL.'/board.php?bo_table=gallery&wr_id='.$row['wr_id'] ?>" class="group">
                    <div class="relative aspect-[4/5] rounded-[2.5rem] overflow-hidden mb-5 shadow-lg shadow-slate-200/50">
                        <img src="<?php echo $img_src ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="Gallery">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="bg-white/20 backdrop-blur-md text-white border border-white/30 px-4 py-2 rounded-xl text-xs font-bold">자세히 보기</span>
                        </div>
                    </div>
                    <h4 class="font-bold text-slate-800 text-center truncate px-2"><?php echo get_text($row['wr_subject']) ?></h4>
                </a>
                <?php } ?>
            </div>
        </section>

        <!-- [3. 유튜브 온라인 예배] -->
        <section class="bg-slate-950 -mx-4 px-6 md:px-12 py-24 rounded-[3.5rem] text-white overflow-hidden relative shadow-2xl">
            <div class="absolute top-0 right-0 w-1/3 h-full bg-blue-600/5 blur-[100px] pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8 mb-16">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <div class="w-16 h-16 bg-rose-600 rounded-2xl flex items-center justify-center shadow-lg shadow-rose-600/30">
                            <?php echo $icons['youtube'] ?>
                        </div>
                        <div class="text-center md:text-left">
                            <h3 class="text-3xl font-black mb-1">유튜브 온라인 예배</h3>
                            <p class="text-slate-400 font-medium">어디서든 함께 예배하며 은혜를 나눕니다.</p>
                        </div>
                    </div>
                    <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=youtube" class="bg-white text-slate-900 px-8 py-3.5 rounded-xl font-bold hover:bg-rose-600 hover:text-white transition-all text-sm shadow-xl">공식 채널 방문</a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <?php
                    $sql = " select * from {$g5['write_prefix']}youtube where wr_is_comment = 0 order by wr_num limit 3 ";
                    $result = sql_query($sql);
                    while ($row = sql_fetch_array($result)) {
                        $yt_id = get_yt_id($row['wr_1'] ?: ($row['wr_link1'] ?: $row['wr_content']));
                        $thumb_src = get_yt_thumb($yt_id);
                    ?>
                    <a href="<?php echo G5_BBS_URL.'/board.php?bo_table=youtube&wr_id='.$row['wr_id'] ?>" class="group">
                        <div class="relative aspect-video rounded-2xl overflow-hidden mb-5 shadow-2xl border border-white/5 bg-slate-900">
                            <img src="<?php echo $thumb_src ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-80 group-hover:opacity-100">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-14 h-14 bg-rose-600 rounded-full flex items-center justify-center shadow-2xl transform scale-90 group-hover:scale-100 transition-transform duration-300">
                                    <?php echo $icons['play'] ?>
                                </div>
                            </div>
                        </div>
                        <h4 class="font-bold text-xl text-white group-hover:text-rose-500 transition-colors line-clamp-2 leading-snug mb-2"><?php echo get_text($row['wr_subject']) ?></h4>
                        <p class="text-slate-500 text-xs font-bold uppercase tracking-widest"><?php echo date("Y.m.d", strtotime($row['wr_datetime'])) ?></p>
                    </a>
                    <?php } ?>
                </div>
            </div>
        </section>

        <!-- [4. 오시는 길] -->
        <section>
            <div class="flex justify-between items-end mb-10">
                <h3 class="text-3xl font-bold text-slate-900 tracking-tight">오시는 길</h3>
                <div class="flex items-center gap-2 text-blue-600 font-bold bg-blue-50 px-5 py-2 rounded-xl text-sm">
                    <?php echo $icons['map-pin'] ?> Cebu City, Philippines
                </div>
            </div>
            
            <div class="rounded-[2.5rem] overflow-hidden shadow-2xl border-[8px] border-white bg-white h-[450px] relative mb-12">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3925.045!2d123.89!3d10.31!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDE4JzM2LjAiTiAxMjPCsDUzJzI0LjAiRQ!5e0!3m2!1sko!2sph!4v123456789" 
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-gray-100 flex flex-col gap-6">
                    <div class="w-12 h-12 bg-white text-blue-600 rounded-2xl flex items-center justify-center shadow-sm"><?php echo $icons['map-pin'] ?></div>
                    <div>
                        <h5 class="font-black text-lg mb-1">교회 주소</h5>
                        <p class="text-slate-500 text-sm leading-relaxed">402 J. Solon Dr, Cebu City, 6000 Cebu</p>
                    </div>
                </div>
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-gray-100 flex flex-col gap-6">
                    <div class="w-12 h-12 bg-white text-green-600 rounded-2xl flex items-center justify-center shadow-sm"><?php echo $icons['calendar'] ?></div>
                    <div>
                        <h5 class="font-black text-lg mb-1">예배 시간</h5>
                        <p class="text-slate-500 text-sm leading-relaxed">주일 1부 09:00 / 2부 11:00<br>금요예배 19:00</p>
                    </div>
                </div>
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-gray-100 flex flex-col gap-6">
                    <div class="w-12 h-12 bg-white text-rose-600 rounded-2xl flex items-center justify-center shadow-sm"><?php echo $icons['youtube'] ?></div>
                    <div>
                        <h5 class="font-black text-lg mb-1">사역 문의</h5>
                        <p class="text-slate-900 font-bold text-lg leading-relaxed">0917-523-0153</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>