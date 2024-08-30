import { get, httpDelete, post, put } from './request.js';

export function getClassicPages() {
  return get('/api/classic-page');
}

export function createClassicPage(title, content) {
  return post('/api/classic-page', {
    title,
    content,
  });
}

export async function updateClassicPage(id, title, content) {
  await put(`/api/classic-page/${id}`, {
    title,
    content,
  });
}

export function getClassicPage(id) {
  return get(`/api/classic-page/${id}`);
}

export async function deleteClassicPage(id) {
  await httpDelete(`/api/classic-page/${id}`);
}
