<?php
return \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "/api/api-key", ["ctrl", "Jinya\Cms\Web\Controllers\ApiKeyController","getApiKeys", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);
$r->addRoute("DELETE", "api/api-key/{key}", ["ctrl", "Jinya\Cms\Web\Controllers\ApiKeyController","deleteApiKey", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("POST", "api/user", ["ctrl", "Jinya\Cms\Web\Controllers\ArtistController","createArtist", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);
$r->addRoute("PUT", "api/user/{id}", ["ctrl", "Jinya\Cms\Web\Controllers\ArtistController","updateArtist", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);
$r->addRoute("DELETE", "api/user/{id}", ["ctrl", "Jinya\Cms\Web\Controllers\ArtistController","deleteArtist", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);
$r->addRoute("PUT", "api/user/{id}/activation", ["ctrl", "Jinya\Cms\Web\Controllers\ArtistController","activateArtist", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);
$r->addRoute("DELETE", "api/user/{id}/activation", ["ctrl", "Jinya\Cms\Web\Controllers\ArtistController","deactivateArtist", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);
$r->addRoute("GET", "api/user/{id}/profilepicture", ["ctrl", "Jinya\Cms\Web\Controllers\ArtistController","getProfilePicture", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute("PUT", "api/user/{id}/profilepicture", ["ctrl", "Jinya\Cms\Web\Controllers\ArtistController","uploadProfilePicture", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);
$r->addRoute("DELETE", "api/user/{id}/profilepicture", ["ctrl", "Jinya\Cms\Web\Controllers\ArtistController","deleteProfilePicture", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("PUT", "api/account/password", ["ctrl", "Jinya\Cms\Web\Controllers\AuthenticationController","changePassword", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'password',
  1 => 'oldPassword',
))]]);
$r->addRoute("POST", "api/login", ["ctrl", "Jinya\Cms\Web\Controllers\AuthenticationController","login", [new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'password',
  1 => 'username',
))]]);
$r->addRoute("POST", "api/2fa", ["ctrl", "Jinya\Cms\Web\Controllers\AuthenticationController","twoFactorCode", [new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'password',
  1 => 'username',
))]]);
$r->addRoute("HEAD", "api/login", ["ctrl", "Jinya\Cms\Web\Controllers\AuthenticationController","validateLogin", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/blog-category/{id}/post", ["ctrl", "Jinya\Cms\Web\Controllers\BlogController","getPostsByCategory", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute("GET", "api/blog-post/{id}/section", ["ctrl", "Jinya\Cms\Web\Controllers\BlogController","getSections", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute("PUT", "api/blog-post/{id}/section", ["ctrl", "Jinya\Cms\Web\Controllers\BlogController","replaceSections", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("POST", "api/maintenance/database/query", ["ctrl", "Jinya\Cms\Web\Controllers\DatabaseController","queryDatabase", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN'),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'query',
))]]);
$r->addRoute("GET", "api/maintenance/database/analyze", ["ctrl", "Jinya\Cms\Web\Controllers\DatabaseController","analyzeDatabase", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/environment", ["ctrl", "Jinya\Cms\Web\Controllers\EnvironmentController","getEnvironment", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/file/{id}/content", ["ctrl", "Jinya\Cms\Web\Controllers\FileController","getFileContent", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute("PUT", "api/file/{id}/content/{position:\d+}", ["ctrl", "Jinya\Cms\Web\Controllers\FileController","uploadChunk", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/file/{id}/content/finish", ["ctrl", "Jinya\Cms\Web\Controllers\FileController","finishUpload", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/file/{id}/content", ["ctrl", "Jinya\Cms\Web\Controllers\FileController","startUpload", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/form/{id}/item", ["ctrl", "Jinya\Cms\Web\Controllers\FormController","getItems", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute("PUT", "api/form/{id}/item", ["ctrl", "Jinya\Cms\Web\Controllers\FormController","replaceItems", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "[{route:(?!api\/)}]", ["ctrl", "Jinya\Cms\Web\Controllers\FrontendController","frontend", []]);
$r->addRoute("POST", "[{route:(?!api\/)}]", ["ctrl", "Jinya\Cms\Web\Controllers\FrontendController","postForm", []]);
$r->addRoute("GET", "{year:\d\d\d\d}/{month:\d\d}/{day:\d\d}/{slug}", ["ctrl", "Jinya\Cms\Web\Controllers\FrontendController","blogFrontend", []]);
$r->addRoute("GET", "api/frontend/links", ["ctrl", "Jinya\Cms\Web\Controllers\FrontendController","getLinks", []]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/gallery/{galleryId}/file", ["ctrl", "Jinya\Cms\Web\Controllers\GalleryController","getPositions", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute("POST", "api/gallery/{galleryId}/file", ["ctrl", "Jinya\Cms\Web\Controllers\GalleryController","createPosition", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER'),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'position',
  1 => 'file',
))]]);
$r->addRoute("DELETE", "api/gallery/{galleryId}/file/{position}", ["ctrl", "Jinya\Cms\Web\Controllers\GalleryController","deletePosition", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/gallery/{galleryId}/file/{position}", ["ctrl", "Jinya\Cms\Web\Controllers\GalleryController","updatePosition", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "install", ["ctrl", "Jinya\Cms\Web\Controllers\InstallController","getInstall", [new Jinya\Cms\Web\Middleware\RedirectInstallerMiddleware()]]);
$r->addRoute("POST", "api/install/configuration", ["ctrl", "Jinya\Cms\Web\Controllers\InstallController","createConfiguration", [new Jinya\Cms\Web\Middleware\RedirectInstallerMiddleware()]]);
$r->addRoute("POST", "api/install/database", ["ctrl", "Jinya\Cms\Web\Controllers\InstallController","createDatabase", [new Jinya\Cms\Web\Middleware\RedirectInstallerMiddleware()]]);
$r->addRoute("POST", "api/install/admin", ["ctrl", "Jinya\Cms\Web\Controllers\InstallController","createAdmin", [new Jinya\Cms\Web\Middleware\RedirectInstallerMiddleware()]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/known-device", ["ctrl", "Jinya\Cms\Web\Controllers\KnownDeviceController","getKnownDevices", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);
$r->addRoute("DELETE", "api/known-device/{key}", ["ctrl", "Jinya\Cms\Web\Controllers\KnownDeviceController","deleteKnownDevice", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);
$r->addRoute("HEAD", "api/known-device/{key}", ["ctrl", "Jinya\Cms\Web\Controllers\KnownDeviceController","validateKnownDevice", []]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/ip-location/{ip}", ["ctrl", "Jinya\Cms\Web\Controllers\LocateIpController","locateIp", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/menu/{id}/item", ["ctrl", "Jinya\Cms\Web\Controllers\MenuController","getItems", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute("PUT", "api/menu/{id}/item", ["ctrl", "Jinya\Cms\Web\Controllers\MenuController","replaceItems", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/modern-page/{id}/section", ["ctrl", "Jinya\Cms\Web\Controllers\ModernPageController","getSections", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute("PUT", "api/modern-page/{id}/section", ["ctrl", "Jinya\Cms\Web\Controllers\ModernPageController","replaceSections", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/me", ["ctrl", "Jinya\Cms\Web\Controllers\MyJinyaController","getMyProfile", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);
$r->addRoute("PUT", "api/me", ["ctrl", "Jinya\Cms\Web\Controllers\MyJinyaController","updateMyProfile", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);
$r->addRoute("PUT", "api/me/colorscheme", ["ctrl", "Jinya\Cms\Web\Controllers\MyJinyaController","updateColorScheme", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);
$r->addRoute("PUT", "api/me/profilepicture", ["ctrl", "Jinya\Cms\Web\Controllers\MyJinyaController","uploadProfilePicture", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL),new Jinya\Cms\Web\Middleware\AuthorizationMiddleware(NULL)]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/php-info", ["ctrl", "Jinya\Cms\Web\Controllers\PhpInfoController","getPhpInfo", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("POST", "api/theme", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","uploadTheme", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateTheme", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/active", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","activateTheme", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/assets", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","compileTheme", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/configuration", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateConfiguration", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("GET", "api/theme/{id}/configuration/structure", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getConfigurationStructure", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("GET", "api/theme/{id}/configuration/default", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getDefaultConfiguration", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("GET", "api/theme/{id}/styling", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getStyleVariables", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/styling", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateStyleVariables", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("GET", "api/theme/{id}/preview", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getPreviewImage", []]);
$r->addRoute("GET", "api/theme/{id}/blog-category", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getThemeBlogCategories", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/blog-category/{name}", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateThemeBlogCategory", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER'),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'blogCategory',
))]]);
$r->addRoute("GET", "api/theme/{id}/classic-page", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getThemeClassicPages", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/classic-page/{name}", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateThemeClassicPage", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER'),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'classicPage',
))]]);
$r->addRoute("GET", "api/theme/{id}/file", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getThemeFiles", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/file/{name}", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateThemeFile", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER'),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'file',
))]]);
$r->addRoute("GET", "api/theme/{id}/form", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getThemeForms", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/form/{name}", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateThemeForm", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER'),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'form',
))]]);
$r->addRoute("GET", "api/theme/{id}/gallery", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getThemeGalleries", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/gallery/{name}", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateThemeGallery", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER'),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'gallery',
))]]);
$r->addRoute("GET", "api/theme/{id}/menu", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getThemeMenus", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/menu/{name}", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateThemeMenu", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER'),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'menu',
))]]);
$r->addRoute("GET", "api/theme/{id}/modern-page", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","getThemeModernPages", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);
$r->addRoute("PUT", "api/theme/{id}/modern-page/{name}", ["ctrl", "Jinya\Cms\Web\Controllers\ThemeController","updateThemeModernPage", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER'),new Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware(array (
  0 => 'modernPage',
))]]);

});
$r->addGroup("/", function (\FastRoute\RouteCollector $r) {
$r->addRoute("GET", "api/version", ["ctrl", "Jinya\Cms\Web\Controllers\UpdateController","getVersionInfo", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);
$r->addRoute("PUT", "api/update", ["ctrl", "Jinya\Cms\Web\Controllers\UpdateController","updateJinya", [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);

});

$jinyaRouterExtensionsRegistrationFunction660f1bc9a41bc = include '/var/www/html/var/cache/jinya/router-extensions/jinya-router-extensions.php';
$jinyaRouterExtensionsRegistrationFunction660f1bc9a41bc($r);
});