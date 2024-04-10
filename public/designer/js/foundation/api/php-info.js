import { get } from './request.js';

// eslint-disable-next-line import/prefer-default-export
export function getPhpInfo() {
  return get('/api/php-info');
}
