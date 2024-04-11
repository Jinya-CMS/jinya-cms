import { get } from './request.js';

export function getEnvironment() {
  return get('/api/environment');
}
