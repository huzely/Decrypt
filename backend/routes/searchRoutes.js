import { Router } from 'express';
import { searchVideos } from '../controllers/videoController.js';

const router = Router();

router.get('/', searchVideos);

export default router;
