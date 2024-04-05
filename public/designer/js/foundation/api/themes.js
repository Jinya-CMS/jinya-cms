import { get, put } from './request.js';

export function getThemes() {
  return get('/api/theme');
}

export function getThemeModernPages(id) {
  return get(`/api/theme/${id}/modern-page`);
}

export function getThemeClassicPages(id) {
  return get(`/api/theme/${id}/classic-page`);
}

export function getThemeForms(id) {
  return get(`/api/theme/${id}/form`);
}

export function getThemeMenus(id) {
  return get(`/api/theme/${id}/menu`);
}

export function getThemeGalleries(id) {
  return get(`/api/theme/${id}/gallery`);
}

export function getThemeFiles(id) {
  return get(`/api/theme/${id}/file`);
}

export function getThemeBlogCategories(id) {
  return get(`/api/theme/${id}/blog-category`);
}

export function getThemeStyleVariables(id) {
  return get(`/api/theme/${id}/styling`);
}

export function getThemeDefaultConfiguration(id) {
  return get(`/api/theme/${id}/configuration/default`);
}

export function getThemeConfigurationStructure(id) {
  return get(`/api/theme/${id}/configuration/structure`);
}

export function updateThemeConfiguration(id, configuration) {
  return put(`/api/theme/${id}/configuration`, configuration);
}

export function updateThemeVariables(id, variables) {
  return put(`/api/theme/${id}/styling`, variables);
}
