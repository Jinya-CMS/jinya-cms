import { get, httpDelete, post, put } from './request.js';

export function getFolders() {
  return get('/api/folder');
}

export async function updateFolder(id, name) {
  await put(`/api/folder/${id}`, {
    name,
  });
}

export function getFolder(id) {
  return get(`/api/folder/${id}`);
}

export async function deleteFolder(id) {
  await httpDelete(`/api/folder/${id}`);
}

export function createFolder(name, parentId) {
  return post('/api/folder', {
    name,
    parentId,
  });
}
