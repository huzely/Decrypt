import { Announcement } from '../models/Announcement.js';

export async function getAnnouncement(req, res) {
  const announcement = await Announcement.findOne({ active: true }).sort({ updatedAt: -1 });
  res.json(announcement);
}

export async function createAnnouncement(req, res) {
  const announcement = await Announcement.create(req.body);
  res.status(201).json(announcement);
}

export async function updateAnnouncement(req, res) {
  const announcement = await Announcement.findByIdAndUpdate(req.params.id, req.body, { new: true, runValidators: true });
  if (!announcement) return res.status(404).json({ message: 'Announcement not found' });
  res.json(announcement);
}

export async function deleteAnnouncement(req, res) {
  const announcement = await Announcement.findByIdAndDelete(req.params.id);
  if (!announcement) return res.status(404).json({ message: 'Announcement not found' });
  res.json({ message: 'Announcement deleted' });
}
