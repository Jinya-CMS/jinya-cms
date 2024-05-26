import { get, httpDelete, post, put } from './request.js';

export function getBlogCategories() {
  return get('/api/blog-category');
}

export function createBlogCategory(name, description, parent, webhookEnabled = false, webhookUrl = null) {
  return post('/api/blog-category', {
    name,
    description,
    parentId: parent,
    webhookEnabled,
    webhookUrl,
  });
}

export async function updateBlogCategory(id, name, description, webhookEnabled = false, webhookUrl = null) {
  await put(`/api/blog-category/${id}`, {
    name,
    description,
    webhookEnabled,
    webhookUrl,
  });
}

export async function deleteBlogCategory(id) {
  await httpDelete(`/api/blog-category/${id}`);
}
