import { get, httpDelete, post, put, upload } from './request.js';

const oneMegaByte = 1024 * 1024;

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

export async function moveFile(id, newFolder) {
  await put(`/api/file/${id}`, {
    folderId: newFolder,
  });
}

export async function tagFile(id, tags) {
  await put(`/api/file/${id}`, {
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

  for (let i = 0; i < file.size; i += oneMegaByte) {
    await uploadChunk(id, i, file.slice(i, i + oneMegaByte));
  }

  await finishUpload(id);
}

export function getFile(id) {
  return get(`/api/file/${id}`);
}

export async function deleteFile(id) {
  await httpDelete(`/api/file/${id}`);
}

export async function createFile(name, tags, folderId, file = null) {
  const savedFile = await post('/api/file', {
    name,
    tags,
    folderId,
  });
  if (file) {
    await uploadFile(savedFile.id, file);

    return getFile(savedFile.id);
  }

  return savedFile;
}
