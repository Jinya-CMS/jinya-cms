import { get, put, upload, uploadPost } from './request.js';

export function getThemes() {
  return get('/api/theme');
}

export function getTheme(id) {
  return get(`/api/theme/${id}`);
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
  return put(`/api/theme/${id}/configuration`, { configuration });
}

export function updateThemeVariables(id, variables) {
  return put(`/api/theme/${id}/styling`, { variables });
}

export function updateThemeModernPage(id, name, page) {
  return put(`/api/theme/${id}/modern-page/${name}`, { modernPage: page });
}

export function updateThemeClassicPage(id, name, page) {
  return put(`/api/theme/${id}/classic-page/${name}`, { classicPage: page });
}

export function updateThemeForm(id, name, form) {
  return put(`/api/theme/${id}/form/${name}`, { form });
}

export function updateThemeMenu(id, name, menu) {
  return put(`/api/theme/${id}/menu/${name}`, { menu });
}

export function updateThemeGallery(id, name, gallery) {
  return put(`/api/theme/${id}/gallery/${name}`, { gallery });
}

export function updateThemeFile(id, name, file) {
  return put(`/api/theme/${id}/file/${name}`, { file });
}

export function updateThemeBlogCategory(id, name, blogCategory) {
  return put(`/api/theme/${id}/blog-category/${name}`, { blogCategory });
}

export function compileThemeAssets(id) {
  return put(`/api/theme/${id}/assets`);
}

export function activateTheme(id) {
  return put(`/api/theme/${id}/active`);
}

export function uploadTheme(file) {
  return uploadPost('/api/theme', file);
}

export function updateTheme(id, file) {
  return upload(`/api/theme/${id}`, file);
}
