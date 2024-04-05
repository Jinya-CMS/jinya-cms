<?php
global $ThemeClassicPage__Columns;

$ThemeClassicPage__Columns = [
'byProperty' => [
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'themeId' => new Jinya\Database\Cache\CacheColumn('themeId', 'theme_id', null, false, true, false, NULL),
'classicPageId' => new Jinya\Database\Cache\CacheColumn('classicPageId', 'page_id', null, false, true, false, NULL),

],
'bySqlName' => [
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'theme_id' => new Jinya\Database\Cache\CacheColumn('themeId', 'theme_id', null, false, true, false, NULL),
'page_id' => new Jinya\Database\Cache\CacheColumn('classicPageId', 'page_id', null, false, true, false, NULL),

],
];