import { Router } from 'express';
import { getDaily, getMonthly, getOverview } from '../controllers/statsController.js';

const router = Router();
router.get('/overview', getOverview);
router.get('/daily', getDaily);
router.get('/monthly', getMonthly);
export default router;
