import { Router } from 'express';
import { getSettings, saveSettings } from '../controllers/settingsController.js';

const router = Router();
router.get('/', getSettings);
router.post('/', saveSettings);
export default router;
