import { get } from './request.js';

export function getBlogPosts(categoryId = null) {
  if (categoryId) {
    return get(`/api/blog-category/${categoryId}/post`);
  }

  return get(`/api/blog-post`);
}
