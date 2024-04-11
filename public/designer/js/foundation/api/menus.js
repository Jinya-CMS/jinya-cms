import { get } from './request.js';

export function getMenus() {
  return get('/api/menu');
}
