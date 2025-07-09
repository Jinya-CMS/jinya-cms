import { httpDelete } from './request.js';

export async function clearCache() {
  await httpDelete('/api/cache');
}
