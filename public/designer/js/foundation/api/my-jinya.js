import { get, put } from './request.js';

export async function getMyProfile() {
  return get('/api/me');
}

export async function setColorScheme(colorScheme) {
  await put('/api/me/colorscheme', { colorScheme });
}
