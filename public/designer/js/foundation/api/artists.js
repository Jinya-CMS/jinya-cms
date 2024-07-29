import { get, httpDelete, post, put } from './request.js';

export function getArtists() {
  return get('/api/user');
}

export function getArtist(id) {
  return get(`/api/user/${id}`);
}

export async function deleteArtist(id) {
  await httpDelete(`/api/user/${id}`);
}

export async function enableArtist(id) {
  await put(`/api/user/${id}/activation`);
}

export async function disableArtist(id) {
  await httpDelete(`/api/user/${id}/activation`);
}

export async function resetTotp(id) {
  await httpDelete(`/api/user/${id}/totp`);
}

export function createArtist(artistName, email, password, isReader, isWriter, isAdmin) {
  const roles = [];
  if (isReader) {
    roles.push('ROLE_READER');
  }
  if (isWriter) {
    roles.push('ROLE_WRITER');
  }
  if (isAdmin) {
    roles.push('ROLE_ADMIN');
  }

  return post('/api/user', {
    artistName,
    email,
    password,
    roles,
    enabled: true,
  });
}

export function updateArtist(id, artistName, email, password, roles) {
  const artist = {
    artistName,
    email,
    roles,
  };
  if (password) {
    artist.password = password;
  }

  return put(`/api/user/${id}`, artist);
}
