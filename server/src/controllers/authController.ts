import { Request, Response } from 'express';
import { login, register, requestPasswordReset, resetPassword } from '../services/authService';

export async function showLogin(req: Request, res: Response) {
  res.render('user/login', { title: 'Login' });
}

export async function showRegister(req: Request, res: Response) {
  res.render('user/register', { title: 'Register' });
}

export async function handleRegister(req: Request, res: Response) {
  const { username, email, password } = req.body;
  try {
    await register(username, email, password);
    res.redirect('/login');
  } catch (error: any) {
    res.render('user/register', { title: 'Register', error: error.message });
  }
}

export async function handleLogin(req: Request, res: Response) {
  const { email, password } = req.body;
  try {
    const { token } = await login(email, password);
    res.cookie('token', token, { httpOnly: true });
    res.redirect('/dashboard');
  } catch (error: any) {
    res.render('user/login', { title: 'Login', error: error.message });
  }
}

export function handleLogout(_req: Request, res: Response) {
  res.clearCookie('token');
  res.redirect('/login');
}

export async function showForgotPassword(_req: Request, res: Response) {
  res.render('user/forgot', { title: 'Quên mật khẩu' });
}

export async function sendReset(req: Request, res: Response) {
  const { email } = req.body;
  try {
    const token = await requestPasswordReset(email);
    res.render('user/forgot', { title: 'Quên mật khẩu', message: `Token reset (demo): ${token.token}` });
  } catch (error: any) {
    res.render('user/forgot', { title: 'Quên mật khẩu', error: error.message });
  }
}

export async function showReset(req: Request, res: Response) {
  res.render('user/reset', { title: 'Reset password', token: req.query.token });
}

export async function handleReset(req: Request, res: Response) {
  const { token, password } = req.body;
  try {
    await resetPassword(token, password);
    res.redirect('/login');
  } catch (error: any) {
    res.render('user/reset', { title: 'Reset password', token, error: error.message });
  }
}
