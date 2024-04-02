import {
  get, httpDelete, post, put, upload,
} from './request.js';

export function getTags() {
  return get('/api/file-tag');
}

export function createTag(name, emoji, color) {
  return post('/api/file-tag', {
    name,
    emoji,
    color,
  });
}

export function updateTag(id, name, emoji, color) {
  return put(`/api/file-tag/${id}`, {
    name,
    emoji,
    color,
  });
}

export function deleteTag(id) {
  return httpDelete(`/api/file-tag/${id}`);
}

export function getFiles() {
  return get('/api/file');
}

export async function updateFile(id, name, tags) {
  await put(`/api/file/${id}`, {
    name,
    tags,
  });
}

export async function startUpload(id) {
  await put(`/api/file/${id}/content`);
}

export async function uploadChunk(id, position, data) {
  await upload(`/api/file/${id}/content/${position}`, data);
}

export async function finishUpload(id) {
  await put(`/api/file/${id}/content/finish`);
}

export async function uploadFile(id, file) {
  try {
    await startUpload(id);
  } catch (e) {
    if (e.status === 409) {
      await finishUpload(id);
      await startUpload(id);
    }
  }
  await uploadChunk(id, 0, file);
  await finishUpload(id);
}

export function getFile(id) {
  return get(`/api/file/${id}`);
}

export async function deleteFile(id) {
  await httpDelete(`/api/file/${id}`);
}

export async function createFile(name, tags, file) {
  const savedFile = await post('/api/file', {
    name,
    tags,
  });
  await uploadFile(savedFile.id, file);

  return getFile(savedFile.id);
}

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
