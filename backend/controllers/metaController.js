import { Category } from '../models/Category.js';
import { Tag } from '../models/Tag.js';

export async function listCategories(req, res) {
  const categories = await Category.find().sort({ name: 1 });
  res.json(categories);
}

export async function createCategory(req, res) {
  const category = await Category.create(req.body);
  res.status(201).json(category);
}

export async function updateCategory(req, res) {
  const category = await Category.findByIdAndUpdate(req.params.id, req.body, { new: true, runValidators: true });
  if (!category) return res.status(404).json({ message: 'Category not found' });
  res.json(category);
}

export async function deleteCategory(req, res) {
  const category = await Category.findByIdAndDelete(req.params.id);
  if (!category) return res.status(404).json({ message: 'Category not found' });
  res.json({ message: 'Category deleted' });
}

export async function listTags(req, res) {
  const tags = await Tag.find().sort({ name: 1 });
  res.json(tags);
}

export async function createTag(req, res) {
  const tag = await Tag.create(req.body);
  res.status(201).json(tag);
}

export async function updateTag(req, res) {
  const tag = await Tag.findByIdAndUpdate(req.params.id, req.body, { new: true, runValidators: true });
  if (!tag) return res.status(404).json({ message: 'Tag not found' });
  res.json(tag);
}

export async function deleteTag(req, res) {
  const tag = await Tag.findByIdAndDelete(req.params.id);
  if (!tag) return res.status(404).json({ message: 'Tag not found' });
  res.json({ message: 'Tag deleted' });
}
