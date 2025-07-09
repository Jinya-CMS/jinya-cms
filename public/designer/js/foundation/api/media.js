import { get } from './request.js';

export function getRootFolder() {
  return get('/api/media');
}
