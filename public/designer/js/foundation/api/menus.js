import { get, httpDelete, post, put } from './request.js';

export function getMenus() {
  return get('/api/menu');
}

export function getMenuItems(id) {
  return get(`/api/menu/${id}/item`);
}

export function createMenu(name, logo = null) {
  return post('/api/menu', {
    name,
    logo,
  });
}

export async function deleteMenu(id) {
  await httpDelete(`/api/menu/${id}`);
}

export async function updateMenu(id, name, logo = null) {
  await put(`/api/menu/${id}`, {
    name,
    logo,
  });
}

export async function updateMenuItems(id, items) {
  await put(`/api/menu/${id}/item`, items);
}
