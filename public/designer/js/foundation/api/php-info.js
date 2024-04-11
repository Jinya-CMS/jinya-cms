import { get } from './request.js';

export function getPhpInfo() {
  return get('/api/php-info');
}
