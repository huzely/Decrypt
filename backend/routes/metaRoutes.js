import { Router } from 'express';
import {
  createCategory,
  createTag,
  deleteCategory,
  deleteTag,
  listCategories,
  listTags,
  updateCategory,
  updateTag
} from '../controllers/metaController.js';

const categoryRouter = Router();
categoryRouter.get('/', listCategories);
categoryRouter.post('/', createCategory);
categoryRouter.put('/:id', updateCategory);
categoryRouter.delete('/:id', deleteCategory);

const tagRouter = Router();
tagRouter.get('/', listTags);
tagRouter.post('/', createTag);
tagRouter.put('/:id', updateTag);
tagRouter.delete('/:id', deleteTag);

export { categoryRouter, tagRouter };
