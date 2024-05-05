import { get } from './request.js';

export function getModernPages() {
  return get('/api/modern-page');
}
