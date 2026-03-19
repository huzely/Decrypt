import { Router } from 'express';
import { createVideo, deleteVideo, getVideoById, getVideos, searchVideos, updateVideo } from '../controllers/videoController.js';

const router = Router();

router.get('/', getVideos);
router.get('/:id', getVideoById);
router.post('/', createVideo);
router.put('/:id', updateVideo);
router.delete('/:id', deleteVideo);

export default router;
