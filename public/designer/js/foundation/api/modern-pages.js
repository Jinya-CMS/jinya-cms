import { get, httpDelete, post, put } from './request.js';

export function getModernPages() {
  return get('/api/modern-page');
}

export function getModernPageSections(id) {
  return get(`/api/modern-page/${id}/section`);
}

export function createModernPage(name) {
  return post('/api/modern-page', {
    name,
  });
}

export async function updateModernPage(id, name) {
  await put(`/api/modern-page/${id}`, {
    name,
  });
}

export async function deleteModernPage(id) {
  await httpDelete(`/api/modern-page/${id}`);
}

export async function updateModernPageSections(id, items) {
  await put(`/api/modern-page/${id}/section`, items);
}
