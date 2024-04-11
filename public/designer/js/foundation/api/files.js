import { get, httpDelete, post, put, upload } from './request.js';

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
