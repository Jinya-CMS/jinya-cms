import { get, post } from './request.js';

export function analyzeDatabase() {
  return get('/api/maintenance/database/analyze');
}

export function executeQueries(queries) {
  return post('/api/maintenance/database/query', { query: queries });
}
