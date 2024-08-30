import { get, httpDelete, post, put } from './request.js';

export function getForms() {
  return get('/api/form');
}

export function getFormItems(id) {
  return get(`/api/form/${id}/item`);
}

export function createForm(title, toAddress, description) {
  return post('/api/form', {
    title,
    toAddress,
    description,
  });
}

export async function updateForm(id, title, toAddress, description) {
  await put(`/api/form/${id}`, {
    title,
    toAddress,
    description,
  });
}

export async function deleteForm(id) {
  await httpDelete(`/api/form/${id}`);
}

export async function updateFormItems(id, items) {
  await put(`/api/form/${id}/item`, items);
}
