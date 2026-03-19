import { Setting } from '../models/Setting.js';

export async function getSettings(req, res) {
  const settings = await Setting.findOne().sort({ updatedAt: -1 });
  res.json(settings);
}

export async function saveSettings(req, res) {
  const existing = await Setting.findOne();
  if (existing) {
    Object.assign(existing, req.body);
    await existing.save();
    return res.json(existing);
  }

  const settings = await Setting.create(req.body);
  res.status(201).json(settings);
}
