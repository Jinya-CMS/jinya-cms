import { get, put } from './request.js';

export function getConfiguration() {
  return get('/api/configuration');
}

export async function updateConfiguration(configuration) {
  await put('/api/configuration', configuration);
}
