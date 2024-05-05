import { get } from './request.js';

export function getBlogCategories() {
  return get('/api/blog-category');
}
