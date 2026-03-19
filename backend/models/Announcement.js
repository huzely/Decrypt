import mongoose from 'mongoose';

const announcementSchema = new mongoose.Schema(
  {
    title: { type: String, required: true },
    content: { type: String, required: true },
    active: { type: Boolean, default: true }
  },
  { timestamps: true }
);

export const Announcement = mongoose.model('Announcement', announcementSchema);
