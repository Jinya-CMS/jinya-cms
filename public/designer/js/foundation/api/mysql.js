import { get } from './request.js';

export function analyzeDatabase() {
  return get('/api/maintenance/database/analyze');
}
