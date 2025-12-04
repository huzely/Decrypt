const express = require('express');
const router = express.Router();
const { loginLimiter } = require('../middleware/rateLimiters');
const controller = require('../controllers/authController');
const { authenticate } = require('../middleware/auth');

router.post('/register', controller.register);
router.post('/login', loginLimiter, controller.login);
router.post('/forgot', controller.forgotPassword);
router.post('/reset', controller.resetPassword);
router.get('/profile', authenticate, controller.profile);
router.post('/change-password', authenticate, controller.changePassword);

module.exports = router;
