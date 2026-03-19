import 'dotenv/config';
import { connectDB } from '../config/db.js';
import { Video } from '../models/Video.js';
import { Category } from '../models/Category.js';
import { Tag } from '../models/Tag.js';
import { Ad } from '../models/Ad.js';
import { Announcement } from '../models/Announcement.js';
import { Setting } from '../models/Setting.js';

const MONGODB_URI = process.env.MONGODB_URI || 'mongodb://127.0.0.1:27017/video_site';

const categories = ['Trending', 'Featured', 'Action', 'Drama', 'Music'];
const tags = ['hot', 'hd', 'exclusive', 'trending', 'viral', 'night', 'cinema'];
const videos = Array.from({ length: 10 }).map((_, index) => ({
  title: `Sample Video ${index + 1}`,
  description: `Demo description for sample video ${index + 1}.`,
  thumbnail: `https://picsum.photos/seed/video-${index + 1}/640/360`,
  videoUrl: index % 2 === 0 ? 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4' : 'https://www.youtube.com/embed/dQw4w9WgXcQ',
  type: index % 2 === 0 ? 'mp4' : 'embed',
  views: 1000 + index * 137,
  tags: [tags[index % tags.length], tags[(index + 2) % tags.length]],
  category: categories[index % categories.length],
  createdAt: new Date(Date.now() - index * 86400000)
}));

const ads = [
  { image: 'https://picsum.photos/seed/header-ad/1200/180', link: 'https://example.com/header', position: 'header', active: true },
  { image: 'https://picsum.photos/seed/middle-ad/1200/180', link: 'https://example.com/middle', position: 'middle', active: true },
  { image: 'https://picsum.photos/seed/footer-ad/1200/180', link: 'https://example.com/footer', position: 'footer', active: true },
  { image: 'https://picsum.photos/seed/popup-ad/1280/720', link: 'https://example.com/popup', position: 'popup', active: true }
];

async function seed() {
  await connectDB(MONGODB_URI);

  await Promise.all([
    Video.deleteMany({}),
    Category.deleteMany({}),
    Tag.deleteMany({}),
    Ad.deleteMany({}),
    Announcement.deleteMany({}),
    Setting.deleteMany({})
  ]);

  await Category.insertMany(categories.map((name) => ({ name })));
  await Tag.insertMany(tags.map((name) => ({ name })));
  await Video.insertMany(videos);
  await Ad.insertMany(ads);
  await Announcement.create({ title: 'Tonight\'s Featured Drop', content: 'New videos are live now. Browse the latest uploads and trending picks.', active: true });
  await Setting.create({ siteName: 'NightFlix', logo: 'https://picsum.photos/seed/logo/120/40', primaryColor: '#e50914', popupAdsEnabled: true });

  console.log('Seed complete');
  process.exit(0);
}

seed();
