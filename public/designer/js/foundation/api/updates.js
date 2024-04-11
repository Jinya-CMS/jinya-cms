import { get, put } from './request.js';

export function getVersion() {
  return get('/api/version');
}

export async function performUpdate() {
  await put('/api/update');
}
