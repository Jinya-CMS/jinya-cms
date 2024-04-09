import { get } from './request.js';

// eslint-disable-next-line import/prefer-default-export
export function getEnvironment() {
  return get('/api/environment');
}
