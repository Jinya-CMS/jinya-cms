<?php
global $ThemeFile__Columns;

$ThemeFile__Columns = [
'byProperty' => [
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'themeId' => new Jinya\Database\Cache\CacheColumn('themeId', 'theme_id', null, false, true, false, NULL),
'fileId' => new Jinya\Database\Cache\CacheColumn('fileId', 'file_id', null, false, true, false, NULL),

],
'bySqlName' => [
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'theme_id' => new Jinya\Database\Cache\CacheColumn('themeId', 'theme_id', null, false, true, false, NULL),
'file_id' => new Jinya\Database\Cache\CacheColumn('fileId', 'file_id', null, false, true, false, NULL),

],
];