<?php
global $ThemeMenu__Columns;

$ThemeMenu__Columns = [
'byProperty' => [
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'themeId' => new Jinya\Database\Cache\CacheColumn('themeId', 'theme_id', null, false, true, false, NULL),
'menuId' => new Jinya\Database\Cache\CacheColumn('menuId', 'menu_id', null, false, true, false, NULL),

],
'bySqlName' => [
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'theme_id' => new Jinya\Database\Cache\CacheColumn('themeId', 'theme_id', null, false, true, false, NULL),
'menu_id' => new Jinya\Database\Cache\CacheColumn('menuId', 'menu_id', null, false, true, false, NULL),

],
];