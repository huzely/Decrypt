import { Router } from 'express';
import { createAnnouncement, deleteAnnouncement, getAnnouncement, updateAnnouncement } from '../controllers/announcementController.js';

const router = Router();
router.get('/', getAnnouncement);
router.post('/', createAnnouncement);
router.put('/:id', updateAnnouncement);
router.delete('/:id', deleteAnnouncement);
export default router;
