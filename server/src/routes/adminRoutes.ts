import { Router } from 'express';
import { adminDashboard, adminHandleLogin, adminLoginPage, assetsPage, depositsPage, handleTx, listUsers, payoutPage, saveAsset, savePayout, toggleUserStatus, withdrawalsPage } from '../controllers/adminController';
import { authenticate, requireAdmin } from '../middlewares/auth';

const router = Router();

router.get('/login', adminLoginPage);
router.post('/login', adminHandleLogin);

router.use(authenticate());
router.use(requireAdmin);

router.get('/', adminDashboard);
router.get('/users', listUsers);
router.post('/users/:id/toggle', toggleUserStatus);
router.get('/deposits', depositsPage);
router.get('/withdrawals', withdrawalsPage);
router.post('/transactions/:id', handleTx);
router.get('/assets', assetsPage);
router.post('/assets', saveAsset);
router.get('/assets/:id/payout', payoutPage);
router.post('/assets/:id/payout', savePayout);

export default router;
