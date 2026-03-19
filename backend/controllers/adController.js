import { Ad } from '../models/Ad.js';

export async function getAds(req, res) {
  const filter = {};
  if (req.query.active === 'true') filter.active = true;
  const ads = await Ad.find(filter).sort({ createdAt: -1 });
  res.json(ads);
}

export async function createAd(req, res) {
  const ad = await Ad.create(req.body);
  res.status(201).json(ad);
}

export async function updateAd(req, res) {
  const ad = await Ad.findByIdAndUpdate(req.params.id, req.body, { new: true, runValidators: true });
  if (!ad) return res.status(404).json({ message: 'Ad not found' });
  res.json(ad);
}

export async function deleteAd(req, res) {
  const ad = await Ad.findByIdAndDelete(req.params.id);
  if (!ad) return res.status(404).json({ message: 'Ad not found' });
  res.json({ message: 'Ad deleted' });
}
