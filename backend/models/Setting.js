import mongoose from 'mongoose';

const settingSchema = new mongoose.Schema(
  {
    siteName: { type: String, default: 'NightFlix' },
    logo: { type: String, default: '' },
    primaryColor: { type: String, default: '#e50914' },
    popupAdsEnabled: { type: Boolean, default: true }
  },
  { timestamps: true }
);

export const Setting = mongoose.model('Setting', settingSchema);
