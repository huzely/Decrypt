import { Router } from 'express';
import { createComment } from '../controllers/commentController.js';

const router = Router();
router.post('/', createComment);
export default router;
