import { Router } from 'express';
import { createAd, deleteAd, getAds, updateAd } from '../controllers/adController.js';

const router = Router();
router.get('/', getAds);
router.post('/', createAd);
router.put('/:id', updateAd);
router.delete('/:id', deleteAd);
export default router;
