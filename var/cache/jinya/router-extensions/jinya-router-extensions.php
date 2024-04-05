<?php
    return function (\FastRoute\RouteCollector $r) {
    $handler = new \Jinya\Router\Extensions\Database\DatabaseRequestHandler();
$r->addRoute('GET', '/api/user', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\Artist::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);
$r->addRoute('GET', '/api/user/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\Artist::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_ADMIN')]]);$r->addRoute('GET', '/api/blog-category', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\BlogCategory::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute('GET', '/api/blog-category/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\BlogCategory::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);$r->addRoute('POST', '/api/blog-category', ['fn', function() use ($handler) {
    return $handler->handleCreateRequest(get_request(true), Jinya\Cms\Database\BlogCategory::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'description' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'parentId' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
  'webhookEnabled' => 
  array (
    'required' => false,
    'type' => 'bool',
    'default' => NULL,
  ),
  'webhookUrl' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
));
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('PUT', '/api/blog-category/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleUpdateRequest(get_request(true), Jinya\Cms\Database\BlogCategory::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'description' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'parentId' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
  'webhookEnabled' => 
  array (
    'required' => false,
    'type' => 'bool',
    'default' => NULL,
  ),
  'webhookUrl' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
), $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('DELETE', '/api/blog-category/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleDeleteRequest(get_request(false), Jinya\Cms\Database\BlogCategory::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('GET', '/api/blog-post', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\BlogPost::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute('GET', '/api/blog-post/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\BlogPost::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);$r->addRoute('POST', '/api/blog-post', ['fn', function() use ($handler) {
    return $handler->handleCreateRequest(get_request(true), Jinya\Cms\Database\BlogPost::class, array (
  'title' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'slug' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'headerImageId' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
  'public' => 
  array (
    'required' => false,
    'type' => 'bool',
    'default' => NULL,
  ),
  'categoryId' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
));
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('PUT', '/api/blog-post/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleUpdateRequest(get_request(true), Jinya\Cms\Database\BlogPost::class, array (
  'title' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'slug' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'headerImageId' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
  'public' => 
  array (
    'required' => false,
    'type' => 'bool',
    'default' => NULL,
  ),
  'categoryId' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
), $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('DELETE', '/api/blog-post/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleDeleteRequest(get_request(false), Jinya\Cms\Database\BlogPost::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('GET', '/api/classic-page', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\ClassicPage::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute('GET', '/api/classic-page/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\ClassicPage::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);$r->addRoute('POST', '/api/classic-page', ['fn', function() use ($handler) {
    return $handler->handleCreateRequest(get_request(true), Jinya\Cms\Database\ClassicPage::class, array (
  'content' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'title' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
));
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('PUT', '/api/classic-page/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleUpdateRequest(get_request(true), Jinya\Cms\Database\ClassicPage::class, array (
  'content' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'title' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
), $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('DELETE', '/api/classic-page/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleDeleteRequest(get_request(false), Jinya\Cms\Database\ClassicPage::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('GET', '/api/file', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\File::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute('GET', '/api/file/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\File::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);$r->addRoute('POST', '/api/file', ['fn', function() use ($handler) {
    return $handler->handleCreateRequest(get_request(true), Jinya\Cms\Database\File::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'tags' => 
  array (
    'required' => false,
    'type' => 'array',
    'default' => NULL,
  ),
));
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('PUT', '/api/file/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleUpdateRequest(get_request(true), Jinya\Cms\Database\File::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'tags' => 
  array (
    'required' => false,
    'type' => 'array',
    'default' => NULL,
  ),
), $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('DELETE', '/api/file/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleDeleteRequest(get_request(false), Jinya\Cms\Database\File::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('GET', '/api/form', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\Form::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute('GET', '/api/form/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\Form::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);$r->addRoute('POST', '/api/form', ['fn', function() use ($handler) {
    return $handler->handleCreateRequest(get_request(true), Jinya\Cms\Database\Form::class, array (
  'title' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'description' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'toAddress' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
));
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('PUT', '/api/form/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleUpdateRequest(get_request(true), Jinya\Cms\Database\Form::class, array (
  'title' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'description' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'toAddress' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
), $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('DELETE', '/api/form/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleDeleteRequest(get_request(false), Jinya\Cms\Database\Form::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('GET', '/api/gallery', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\Gallery::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute('GET', '/api/gallery/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\Gallery::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);$r->addRoute('POST', '/api/gallery', ['fn', function() use ($handler) {
    return $handler->handleCreateRequest(get_request(true), Jinya\Cms\Database\Gallery::class, array (
  'creatorId' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
  'updatedById' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
  'createdAt' => 
  array (
    'required' => false,
    'type' => 'DateTime',
    'default' => NULL,
  ),
  'lastUpdatedAt' => 
  array (
    'required' => false,
    'type' => 'DateTime',
    'default' => NULL,
  ),
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'description' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'type' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'orientation' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
));
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('PUT', '/api/gallery/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleUpdateRequest(get_request(true), Jinya\Cms\Database\Gallery::class, array (
  'creatorId' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
  'updatedById' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
  'createdAt' => 
  array (
    'required' => false,
    'type' => 'DateTime',
    'default' => NULL,
  ),
  'lastUpdatedAt' => 
  array (
    'required' => false,
    'type' => 'DateTime',
    'default' => NULL,
  ),
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'description' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'type' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'orientation' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
), $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('DELETE', '/api/gallery/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleDeleteRequest(get_request(false), Jinya\Cms\Database\Gallery::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('GET', '/api/menu', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\Menu::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute('GET', '/api/menu/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\Menu::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);$r->addRoute('POST', '/api/menu', ['fn', function() use ($handler) {
    return $handler->handleCreateRequest(get_request(true), Jinya\Cms\Database\Menu::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'logo' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
));
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('PUT', '/api/menu/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleUpdateRequest(get_request(true), Jinya\Cms\Database\Menu::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'logo' => 
  array (
    'required' => false,
    'type' => 'int',
    'default' => NULL,
  ),
), $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('DELETE', '/api/menu/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleDeleteRequest(get_request(false), Jinya\Cms\Database\Menu::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('GET', '/api/modern-page', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\ModernPage::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute('GET', '/api/modern-page/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\ModernPage::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);$r->addRoute('POST', '/api/modern-page', ['fn', function() use ($handler) {
    return $handler->handleCreateRequest(get_request(true), Jinya\Cms\Database\ModernPage::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
));
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('PUT', '/api/modern-page/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleUpdateRequest(get_request(true), Jinya\Cms\Database\ModernPage::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
), $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('DELETE', '/api/modern-page/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleDeleteRequest(get_request(false), Jinya\Cms\Database\ModernPage::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('GET', '/api/theme', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\Theme::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER'), new Jinya\Cms\Web\Middleware\ThemeSyncMiddleware()]]);
$r->addRoute('GET', '/api/theme/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\Theme::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER'), new Jinya\Cms\Web\Middleware\ThemeSyncMiddleware()]]);$r->addRoute('GET', '/api/file-tag', ['fn', function() use ($handler) {
    return $handler->handleGetAllRequest(get_request(false), Jinya\Cms\Database\FileTag::class);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);
$r->addRoute('GET', '/api/file-tag/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleGetByIdRequest(get_request(false), Jinya\Cms\Database\FileTag::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_READER')]]);$r->addRoute('POST', '/api/file-tag', ['fn', function() use ($handler) {
    return $handler->handleCreateRequest(get_request(true), Jinya\Cms\Database\FileTag::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'color' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'emoji' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
));
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('PUT', '/api/file-tag/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleUpdateRequest(get_request(true), Jinya\Cms\Database\FileTag::class, array (
  'name' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'color' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
  'emoji' => 
  array (
    'required' => false,
    'type' => 'string',
    'default' => NULL,
  ),
), $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);$r->addRoute('DELETE', '/api/file-tag/{id}', ['fn', function(string|int $id) use ($handler) {
    return $handler->handleDeleteRequest(get_request(false), Jinya\Cms\Database\FileTag::class, $id);
}, [new Jinya\Cms\Web\Middleware\AuthorizationMiddleware('ROLE_WRITER')]]);};