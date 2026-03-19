import mongoose from 'mongoose';

const adSchema = new mongoose.Schema(
  {
    image: { type: String, required: true },
    link: { type: String, required: true },
    position: { type: String, enum: ['popup', 'header', 'middle', 'footer'], required: true },
    active: { type: Boolean, default: true }
  },
  { timestamps: true }
);

export const Ad = mongoose.model('Ad', adSchema);
