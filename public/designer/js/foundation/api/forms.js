import { get } from './request.js';

export function getForms() {
  return get('/api/form');
}
