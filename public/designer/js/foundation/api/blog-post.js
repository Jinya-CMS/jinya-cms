import { get, httpDelete, post, put } from './request.js';

export function getBlogPosts(categoryId = null) {
  if (categoryId) {
    return get(`/api/blog-category/${categoryId}/post`);
  }

  return get('/api/blog-post');
}

export async function updateBlogPost(id, title, slug, categoryId, headerImageId = null) {
  await put(`/api/blog-post/${id}`, {
    title,
    slug,
    categoryId,
    headerImageId,
  });
}

export async function publishBlogPost(id) {
  await put(`/api/blog-post/${id}`, {
    public: true,
  });
}

export async function unpublishBlogPost(id) {
  await put(`/api/blog-post/${id}`, {
    public: false,
  });
}

export function createBlogPost(title, slug, categoryId, headerImageId = null) {
  return post('/api/blog-post', {
    title,
    slug,
    categoryId,
    headerImageId,
  });
}

export async function deleteBlogPost(id) {
  await httpDelete(`/api/blog-post/${id}`);
}

export function getBlogPostSections(id) {
  return get(`/api/blog-post/${id}/section`);
}

export async function updateBlogPostSections(id, items) {
  await put(`/api/blog-post/${id}/section`, items);
}
