import {
  get, httpDelete, post, put,
} from './request.js';

export function getGalleries() {
  return get('/api/gallery');
}

export function getFilesByGallery(galleryId) {
  return get(`/api/gallery/${galleryId}/file`);
}

export async function deleteFileFromGallery(galleryId, position) {
  await httpDelete(`/api/gallery/${galleryId}/file/${position}`);
}

export async function moveFileInGallery(galleryId, oldPosition, newPosition) {
  await put(`/api/gallery/${galleryId}/file/${oldPosition}`, { newPosition });
}

export function addFileToGallery(galleryId, position, file) {
  return post(`/api/gallery/${galleryId}/file`, {
    position,
    file,
  });
}

export function createGallery(name, orientation, type, description) {
  return post('/api/gallery', {
    name,
    orientation,
    type,
    description,
  });
}

export async function updateGallery(id, name, orientation, type, description) {
  await put(`/api/gallery/${id}`, {
    name,
    orientation,
    type,
    description,
  });
}

export function getGallery(id) {
  return get(`/api/gallery/${id}`);
}

export async function deleteGallery(id) {
  httpDelete(`/api/gallery/${id}`);
}
