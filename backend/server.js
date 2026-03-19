import 'dotenv/config';
import express from 'express';
import cors from 'cors';
import morgan from 'morgan';
import { connectDB } from './config/db.js';
import videoRoutes from './routes/videoRoutes.js';
import searchRoutes from './routes/searchRoutes.js';
import commentRoutes from './routes/commentRoutes.js';
import { categoryRouter, tagRouter } from './routes/metaRoutes.js';
import statsRoutes from './routes/statsRoutes.js';
import adRoutes from './routes/adRoutes.js';
import announcementRoutes from './routes/announcementRoutes.js';
import settingsRoutes from './routes/settingsRoutes.js';

const app = express();
const PORT = process.env.PORT || 3000;
const MONGODB_URI = process.env.MONGODB_URI || 'mongodb://127.0.0.1:27017/video_site';

app.use(cors({ origin: process.env.FRONTEND_URL || 'http://localhost:5173' }));
app.use(express.json());
app.use(morgan('dev'));

app.get('/api/health', (req, res) => {
  res.json({ status: 'ok' });
});

app.use('/api/videos', videoRoutes);
app.use('/api/search', searchRoutes);
app.use('/api/comments', commentRoutes);
app.use('/api/categories', categoryRouter);
app.use('/api/tags', tagRouter);
app.use('/api/stats', statsRoutes);
app.use('/api/ads', adRoutes);
app.use('/api/announcement', announcementRoutes);
app.use('/api/settings', settingsRoutes);

app.use((error, req, res, next) => {
  console.error(error);
  res.status(500).json({ message: 'Internal server error' });
});

connectDB(MONGODB_URI).then(() => {
  app.listen(PORT, () => {
    console.log(`Backend running on port ${PORT}`);
  });
});
