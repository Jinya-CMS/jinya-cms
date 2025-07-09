import { get } from './request.js';

export function getStatisticsByGroup(group, months = 1) {
  return get(`/api/statistics/${group}/${months}/month`);
}

export function getTotalVisitsForInterval(months = 1) {
  return get(`/api/statistics/visits/${months}/month`);
}

export function getStatisticsByEntityAndId(type, id, months = 1) {
  return get(`/api/statistics/visits/${months}/month/${type}/${id}`);
}
