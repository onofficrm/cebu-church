import React, { useState, useEffect, useRef } from 'react';
import { 
  Search, 
  Settings, 
  Rss, 
  PenSquare, 
  MoreHorizontal, 
  Eye, 
  MessageSquare, 
  ThumbsUp, 
  ThumbsDown, 
  Lock, 
  Megaphone,
  X,
  ChevronLeft,
  ChevronRight,
  Info
} from 'lucide-react';

// --- Types ---

interface Post {
  id: number;
  subject: string;
  content_summary: string;
  author: string;
  date: string;
  views: number;
  comments: number;
  likes: number;
  dislikes: number;
  is_notice: boolean;
  is_secret: boolean;
  is_new: boolean;
  is_hot: boolean;
  category: string;
  thumbnail?: string;
}

// --- Mock Data ---

const CATEGORIES = ['전체', '공지', '자유', '질문', '정보', '유머', '갤러리'];

const SAMPLE_POSTS: Post[] = [
  {
    id: 101,
    subject: "[필독] 커뮤니티 이용 규정 안내드립니다.",
    content_summary: "쾌적한 커뮤니티 환경을 위해 회원 여러분께서는 반드시 공지사항을 숙지해주시기 바랍니다. 욕설, 비방, 광고성 게시물은 통보 없이 삭제될 수 있습니다.",
    author: "관리자",
    date: "2023.10.25",
    views: 15240,
    comments: 42,
    likes: 120,
    dislikes: 2,
    is_notice: true,
    is_secret: false,
    is_new: false,
    is_hot: true,
    category: "공지"
  },
  {
    id: 102,
    subject: "리액트로 게시판 만드는 팁 공유합니다.",
    content_summary: "안녕하세요. 오늘은 리액트와 테일윈드를 사용하여 게시판 리스트를 이쁘게 꾸미는 방법에 대해 알아보겠습니다. 컴포넌트 분리가 핵심입니다.",
    author: "개발자1",
    date: "10:30",
    views: 342,
    comments: 15,
    likes: 24,
    dislikes: 0,
    is_notice: false,
    is_secret: false,
    is_new: true,
    is_hot: true,
    category: "정보",
    thumbnail: "https://picsum.photos/300/200?random=1"
  },
  {
    id: 103,
    subject: "이 부분 에러가 나는데 원인을 모르겠어요 ㅠㅠ",
    content_summary: "비밀글입니다.",
    author: "뉴비",
    date: "09:15",
    views: 56,
    comments: 2,
    likes: 0,
    dislikes: 0,
    is_notice: false,
    is_secret: true,
    is_new: true,
    is_hot: false,
    category: "질문"
  },
  {
    id: 104,
    subject: "주말에 다녀온 여행 사진 몇 장 올립니다.",
    content_summary: "날씨가 너무 좋아서 근교로 드라이브 다녀왔습니다. 맛집 정보도 같이 공유해드릴게요. 사진 용량이 좀 크네요.",
    author: "여행가",
    date: "2023.10.27",
    views: 890,
    comments: 21,
    likes: 56,
    dislikes: 1,
    is_notice: false,
    is_secret: false,
    is_new: false,
    is_hot: false,
    category: "갤러리",
    thumbnail: "https://picsum.photos/300/200?random=2"
  },
  {
    id: 105,
    subject: "오늘 점심 메뉴 추천 받습니다.",
    content_summary: "매일 점심 뭐 먹을지가 제일 큰 고민이네요. 한식, 중식, 일식 중 뭐가 좋을까요? 회사 근처 맛집이 없어서 슬픕니다.",
    author: "직장인",
    date: "2023.10.26",
    views: 120,
    comments: 8,
    likes: 5,
    dislikes: 0,
    is_notice: false,
    is_secret: false,
    is_new: false,
    is_hot: false,
    category: "자유"
  },
  {
    id: 106,
    subject: "비밀 문의 드립니다.",
    content_summary: "비밀글입니다.",
    author: "익명",
    date: "2023.10.26",
    views: 12,
    comments: 1,
    likes: 0,
    dislikes: 0,
    is_notice: false,
    is_secret: true,
    is_new: false,
    is_hot: false,
    category: "질문"
  }
];

// --- Sub Components ---

const Badge = ({ type }: { type: 'new' | 'hot' | 'notice' }) => {
  const styles = {
    new: "bg-red-100 text-red-600 border-red-200",
    hot: "bg-orange-100 text-orange-600 border-orange-200",
    notice: "bg-blue-100 text-blue-600 border-blue-200"
  };
  
  const labels = {
    new: "새글",
    hot: "인기",
    notice: "공지"
  };

  return (
    <span className={`text-[10px] font-bold px-1.5 py-0.5 rounded border ${styles[type]} ml-1.5 align-middle inline-block`}>
      {labels[type]}
    </span>
  );
};

const PointInfoPopup = ({ isOpen, onClose }: { isOpen: boolean; onClose: () => void }) => {
  const popupRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (popupRef.current && !popupRef.current.contains(event.target as Node)) {
        onClose();
      }
    };
    if (isOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    }
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [isOpen, onClose]);

  if (!isOpen) return null;

  return (
    <div ref={popupRef} className="absolute z-50 top-8 left-0 w-64 bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden animate-fade-in-down">
      <div className="bg-slate-50 px-4 py-3 border-b border-gray-100">
        <h6 className="text-sm font-bold text-slate-800">게시판 포인트 정책</h6>
      </div>
      <div className="p-4 space-y-3">
        {[
          { label: '글읽기', point: 10 },
          { label: '글쓰기', point: 100 },
          { label: '댓글', point: 50 },
          { label: '다운로드', point: -20 },
        ].map((item, idx) => (
          <div key={idx} className="flex justify-between items-center text-sm">
            <span className="text-slate-500">{item.label}</span>
            <span className={`font-bold ${item.point < 0 ? 'text-red-500' : 'text-blue-600'}`}>
              {item.point > 0 ? `+${item.point}` : item.point} P
            </span>
          </div>
        ))}
      </div>
    </div>
  );
};

const ListItem: React.FC<{ post: Post; isCheckbox: boolean }> = ({ post, isCheckbox }) => {
  return (
    <div className="group relative bg-white border border-gray-200 rounded-xl p-4 sm:p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row gap-4 mb-3">
      
      {/* Selection Checkbox (Absolute on mobile, aligned on desktop) */}
      {isCheckbox && (
        <div className="absolute top-4 right-4 sm:static sm:top-auto sm:right-auto flex items-start pt-1">
           <input type="checkbox" className="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" />
        </div>
      )}

      {/* Main Content Area */}
      <div className="flex-1 flex flex-col justify-between min-w-0">
        <div>
          {/* Top Meta: Number/Notice + Category + Date */}
          <div className="flex items-center gap-2 text-xs text-slate-500 mb-2">
            {post.is_notice ? (
               <Megaphone className="w-4 h-4 text-blue-600" />
            ) : (
               <span className="font-mono text-slate-400">{post.id}</span>
            )}
            <span className="w-[1px] h-3 bg-gray-300 mx-1"></span>
            <span className="font-medium text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{post.category}</span>
            <span className="hidden sm:inline text-slate-400">•</span>
            <span className="text-slate-400">{post.date}</span>
          </div>

          {/* Subject */}
          <h3 className="text-base sm:text-lg font-bold text-slate-800 mb-1 group-hover:text-blue-600 transition-colors line-clamp-1 flex items-center">
            {post.is_secret && <Lock className="w-3.5 h-3.5 mr-1.5 text-slate-400" />}
            <a href="#" className="align-middle">
              {post.subject}
            </a>
            {post.is_new && <Badge type="new" />}
            {post.is_hot && <Badge type="hot" />}
            {post.is_notice && <Badge type="notice" />}
          </h3>

          {/* Summary */}
          <p className="text-sm text-slate-500 line-clamp-2 mb-3 leading-relaxed">
            {post.is_secret ? "비밀글입니다." : post.content_summary}
          </p>
        </div>

        {/* Bottom Meta: Author + Stats */}
        <div className="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs sm:text-sm text-slate-500 mt-1">
          <span className="font-medium text-slate-700">{post.author}</span>
          
          <div className="flex items-center gap-3 ml-auto sm:ml-0">
            <div className="flex items-center gap-1" title="조회수">
              <Eye className="w-3.5 h-3.5" />
              <span>{post.views.toLocaleString()}</span>
            </div>
            {post.comments > 0 && (
              <div className="flex items-center gap-1 text-slate-700 font-medium" title="댓글">
                <MessageSquare className="w-3.5 h-3.5" />
                <span>{post.comments}</span>
              </div>
            )}
            {post.likes > 0 && (
              <div className="flex items-center gap-1 text-red-500" title="추천">
                <ThumbsUp className="w-3.5 h-3.5" />
                <span>{post.likes}</span>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Thumbnail (Right side on desktop) */}
      {post.thumbnail && !post.is_secret && (
        <div className="hidden sm:block w-[120px] h-[90px] flex-shrink-0">
          <img 
            src={post.thumbnail} 
            alt="thumbnail" 
            className="w-full h-full object-cover rounded-lg border border-gray-100"
          />
        </div>
      )}
      
      {/* Mobile Thumbnail (If needed, typically hidden or different layout, keeping clean for now) */}
      {post.thumbnail && !post.is_secret && (
        <div className="block sm:hidden w-full h-40 mt-2">
           <img 
            src={post.thumbnail} 
            alt="thumbnail" 
            className="w-full h-full object-cover rounded-lg"
          />
        </div>
      )}

    </div>
  );
};

const SearchModal = ({ isOpen, onClose }: { isOpen: boolean; onClose: () => void }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[100] flex items-start justify-center pt-20 bg-black/50 backdrop-blur-sm animate-fade-in">
      <div className="w-full max-w-2xl bg-white rounded-2xl shadow-2xl p-6 mx-4 relative animate-slide-up">
        <button onClick={onClose} className="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
          <X className="w-6 h-6" />
        </button>
        <h3 className="text-xl font-bold text-slate-800 mb-6">게시글 검색</h3>
        
        <form className="space-y-4">
          <div className="flex flex-col sm:flex-row gap-3">
             <select className="px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-slate-700 min-w-[120px]">
               <option value="subject">제목</option>
               <option value="content">내용</option>
               <option value="subject_content">제목+내용</option>
               <option value="name">글쓴이</option>
             </select>
             <div className="flex-1 relative">
                <input 
                  type="text" 
                  placeholder="검색어를 입력해주세요" 
                  className="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                  autoFocus
                />
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
             </div>
             <button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition-colors">
               검색
             </button>
          </div>
        </form>
      </div>
    </div>
  );
};

// --- Main App ---

export default function BoardList() {
  const [activeCategory, setActiveCategory] = useState('전체');
  const [isPointOpen, setIsPointOpen] = useState(false);
  const [isSearchOpen, setIsSearchOpen] = useState(false);
  const [selectAll, setSelectAll] = useState(false);

  // Filter Logic (Simple Mock)
  const filteredPosts = activeCategory === '전체' 
    ? SAMPLE_POSTS 
    : SAMPLE_POSTS.filter(post => post.category === activeCategory);

  return (
    <div className="min-h-screen py-8 px-4 sm:px-6">
      
      <div className="max-w-5xl mx-auto">
        
        {/* Header Actions */}
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
          <h1 className="text-2xl font-bold text-slate-800 tracking-tight">커뮤니티</h1>
          
          <div className="flex items-center gap-2 self-end sm:self-auto">
             <button className="p-2 text-slate-500 hover:bg-white hover:text-blue-600 hover:shadow-sm rounded-lg transition-all" title="관리">
               <Settings className="w-5 h-5" />
             </button>
             <button 
                onClick={() => setIsSearchOpen(true)}
                className="p-2 text-slate-500 hover:bg-white hover:text-blue-600 hover:shadow-sm rounded-lg transition-all" 
                title="검색"
             >
               <Search className="w-5 h-5" />
             </button>
             <button className="p-2 text-slate-500 hover:bg-white hover:text-orange-500 hover:shadow-sm rounded-lg transition-all" title="RSS">
               <Rss className="w-5 h-5" />
             </button>
             <button className="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm shadow-blue-200 transition-all">
               <PenSquare className="w-4 h-4" />
               <span>글쓰기</span>
             </button>
          </div>
        </div>

        {/* Toolbar (Point Info & Stats) */}
        <div className="flex flex-col sm:flex-row justify-between items-center bg-white p-3 rounded-xl border border-gray-200 shadow-sm mb-6 gap-3">
           <div className="flex items-center gap-4 w-full sm:w-auto">
              <div className="relative">
                 <button 
                  onClick={(e) => { e.stopPropagation(); setIsPointOpen(!isPointOpen); }}
                  className={`flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors ${isPointOpen ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-gray-50'}`}
                 >
                   <Info className="w-4 h-4" />
                   <span>포인트정책</span>
                 </button>
                 <PointInfoPopup isOpen={isPointOpen} onClose={() => setIsPointOpen(false)} />
              </div>

              <div className="h-4 w-[1px] bg-gray-300 hidden sm:block"></div>
              
              <div className="flex items-center gap-2">
                 <input 
                    type="checkbox" 
                    id="selectAll" 
                    checked={selectAll}
                    onChange={() => setSelectAll(!selectAll)}
                    className="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" 
                  />
                 <label htmlFor="selectAll" className="text-sm text-slate-600 cursor-pointer select-none">전체선택</label>
              </div>
           </div>

           <div className="text-sm text-slate-500 w-full sm:w-auto text-right bg-gray-50 sm:bg-transparent px-3 py-1.5 rounded sm:p-0">
             전체 <span className="font-bold text-slate-800">{filteredPosts.length}</span>건 / 1 페이지
           </div>
        </div>

        {/* Category Navigation (Horizontal Scroll) */}
        <div className="mb-6 relative group">
           <div className="overflow-x-auto no-scrollbar pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
             <div className="flex gap-2 min-w-max">
               {CATEGORIES.map((cat) => (
                 <button
                   key={cat}
                   onClick={() => setActiveCategory(cat)}
                   className={`px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 whitespace-nowrap ${
                     activeCategory === cat 
                       ? 'bg-slate-800 text-white shadow-md shadow-slate-200' 
                       : 'bg-white text-slate-500 border border-gray-200 hover:border-slate-300 hover:bg-gray-50'
                   }`}
                 >
                   {cat}
                 </button>
               ))}
             </div>
           </div>
           {/* Fade effect for scroll hint */}
           <div className="absolute right-0 top-0 bottom-2 w-12 bg-gradient-to-l from-[#f8fafc] to-transparent sm:hidden pointer-events-none"></div>
        </div>

        {/* Post List */}
        <div className="space-y-3 mb-8">
           {filteredPosts.length > 0 ? (
             filteredPosts.map(post => (
               <ListItem key={post.id} post={post} isCheckbox={true} />
             ))
           ) : (
             <div className="py-20 text-center bg-white rounded-xl border border-gray-200 border-dashed">
               <div className="text-slate-400 mb-2">게시물이 없습니다.</div>
               <button className="text-blue-600 hover:underline text-sm font-medium">첫 번째 글을 작성해보세요</button>
             </div>
           )}
        </div>

        {/* Footer Actions & Pagination */}
        <div className="flex flex-col-reverse md:flex-row justify-between items-center gap-6">
           {/* Admin Batch Actions */}
           <div className="flex gap-2 w-full md:w-auto">
              <button className="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors">선택삭제</button>
              <button className="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-gray-50 transition-colors">복사</button>
              <button className="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-gray-50 transition-colors">이동</button>
           </div>

           {/* Pagination */}
           <div className="flex items-center gap-1 bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
              <button className="p-2 text-slate-400 hover:text-slate-600 disabled:opacity-50"><ChevronLeft className="w-4 h-4" /></button>
              <button className="w-8 h-8 flex items-center justify-center rounded bg-blue-50 text-blue-600 font-bold text-sm">1</button>
              <button className="w-8 h-8 flex items-center justify-center rounded text-slate-500 hover:bg-gray-50 text-sm">2</button>
              <button className="w-8 h-8 flex items-center justify-center rounded text-slate-500 hover:bg-gray-50 text-sm">3</button>
              <button className="w-8 h-8 flex items-center justify-center rounded text-slate-500 hover:bg-gray-50 text-sm">4</button>
              <button className="p-2 text-slate-400 hover:text-slate-600"><ChevronRight className="w-4 h-4" /></button>
           </div>
           
           {/* Mobile: Write button again for convenience */}
           <div className="md:hidden w-full">
             <button className="w-full bg-blue-600 text-white py-3 rounded-lg font-bold shadow-lg shadow-blue-200">
               글 등록하기
             </button>
           </div>
        </div>

      </div>

      {/* Overlays */}
      <SearchModal isOpen={isSearchOpen} onClose={() => setIsSearchOpen(false)} />

    </div>
  );
}