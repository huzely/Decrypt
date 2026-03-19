import { useEffect, useMemo, useState } from 'react';
import { Route, Routes, useLocation, useNavigate, useParams } from 'react-router-dom';
import { api } from './api/client';
import { Layout } from './components/Layout';
import { PopupAd } from './components/PopupAd';
import { HomePage } from './pages/HomePage';
import { VideoDetailPage } from './pages/VideoDetailPage';
import { UploadPage } from './pages/UploadPage';
import { AdminPage } from './pages/AdminPage';

function VideoDetailRoute({ fetchVideoDetail, videoDetail, loadingVideo }) {
  const { id } = useParams();
  useEffect(() => {
    fetchVideoDetail(id);
  }, [id]);
  return <VideoDetailPage video={videoDetail} loading={loadingVideo} onRefresh={() => fetchVideoDetail(id)} />;
}

export default function App() {
  const [videos, setVideos] = useState([]);
  const [categories, setCategories] = useState([]);
  const [tags, setTags] = useState([]);
  const [ads, setAds] = useState([]);
  const [announcement, setAnnouncement] = useState(null);
  const [settings, setSettings] = useState(null);
  const [stats, setStats] = useState(null);
  const [daily, setDaily] = useState([]);
  const [monthly, setMonthly] = useState([]);
  const [loadingVideos, setLoadingVideos] = useState(true);
  const [loadingVideo, setLoadingVideo] = useState(false);
  const [loadingStats, setLoadingStats] = useState(true);
  const [videoDetail, setVideoDetail] = useState(null);
  const [search, setSearch] = useState('');
  const location = useLocation();
  const navigate = useNavigate();
  const query = useMemo(() => new URLSearchParams(location.search), [location.search]);
  const selectedCategory = query.get('category') || '';
  const selectedTag = query.get('tag') || '';

  const loadInitial = async () => {
    setLoadingVideos(true);
    const params = {};
    if (selectedCategory) params.category = selectedCategory;
    if (selectedTag) params.tag = selectedTag;
    if (search.trim()) params.q = search.trim();

    const [videosRes, categoriesRes, tagsRes, adsRes, announcementRes, settingsRes] = await Promise.all([
      api.get('/videos', { params }),
      api.get('/categories'),
      api.get('/tags'),
      api.get('/ads', { params: { active: true } }),
      api.get('/announcement'),
      api.get('/settings')
    ]);

    setVideos(videosRes.data);
    setCategories(categoriesRes.data);
    setTags(tagsRes.data);
    setAds(adsRes.data);
    setAnnouncement(announcementRes.data);
    setSettings(settingsRes.data);
    setLoadingVideos(false);
  };

  const loadStats = async () => {
    setLoadingStats(true);
    const [overviewRes, dailyRes, monthlyRes] = await Promise.all([
      api.get('/stats/overview'),
      api.get('/stats/daily'),
      api.get('/stats/monthly')
    ]);
    setStats(overviewRes.data);
    setDaily(dailyRes.data);
    setMonthly(monthlyRes.data);
    setLoadingStats(false);
  };

  const fetchVideoDetail = async (id) => {
    setLoadingVideo(true);
    const response = await api.get(`/videos/${id}`);
    setVideoDetail(response.data);
    setLoadingVideo(false);
  };

  useEffect(() => {
    loadInitial();
  }, [selectedCategory, selectedTag, search]);

  useEffect(() => {
    loadStats();
  }, []);

  const setFilter = (key, value) => {
    const next = new URLSearchParams(location.search);
    if (value) next.set(key, value);
    else next.delete(key);
    navigate({ pathname: '/', search: next.toString() });
  };

  return (
    <Layout settings={settings} search={search} onSearchChange={setSearch}>
      <PopupAd ad={ads.find((ad) => ad.position === 'popup')} enabled={Boolean(settings?.popupAdsEnabled)} />
      <Routes>
        <Route path="/" element={<HomePage videos={videos} loading={loadingVideos} categories={categories} selectedCategory={selectedCategory} onCategoryChange={(value) => setFilter('category', value)} selectedTag={selectedTag} onTagChange={(value) => setFilter('tag', value)} announcement={announcement} ads={ads} />} />
        <Route path="/video/:id" element={<VideoDetailRoute fetchVideoDetail={fetchVideoDetail} videoDetail={videoDetail} loadingVideo={loadingVideo} />} />
        <Route path="/upload" element={<UploadPage onUploaded={loadInitial} />} />
        <Route path="/admin" element={<AdminPage stats={stats} daily={daily} monthly={monthly} loadingStats={loadingStats} videos={videos} categories={categories} tags={tags} ads={ads} announcement={announcement} settings={settings} refreshAll={() => { loadInitial(); loadStats(); }} />} />
      </Routes>
    </Layout>
  );
}
