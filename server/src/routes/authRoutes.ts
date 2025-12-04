import { Router } from 'express';
import { handleLogin, handleLogout, handleRegister, sendReset, showForgotPassword, showLogin, showRegister, showReset, handleReset } from '../controllers/authController';
import { loginLimiter } from '../middlewares/rateLimit';

const router = Router();

router.get('/login', showLogin);
router.get('/register', showRegister);
router.get('/forgot', showForgotPassword);
router.get('/reset', showReset);

router.post('/login', loginLimiter, handleLogin);
router.post('/register', handleRegister);
router.post('/forgot', sendReset);
router.post('/reset', handleReset);
router.post('/logout', handleLogout);

export default router;
