<?php
global $Theme__Columns;

$Theme__Columns = [
'byProperty' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'configuration' => new Jinya\Database\Cache\CacheColumn('configuration', 'configuration', new Jinya\Cms\Database\Converter\JsonConverter(), false, true, false, NULL),
'description' => new Jinya\Database\Cache\CacheColumn('description', 'description', new Jinya\Cms\Database\Converter\ThemeDescriptionConverter(), false, true, false, NULL),
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'displayName' => new Jinya\Database\Cache\CacheColumn('displayName', 'display_name', null, false, true, false, NULL),
'scssVariables' => new Jinya\Database\Cache\CacheColumn('scssVariables', 'scss_variables', new Jinya\Cms\Database\Converter\JsonConverter(), false, true, false, NULL),
'hasApiTheme' => new Jinya\Database\Cache\CacheColumn('hasApiTheme', 'has_api_theme', new Jinya\Cms\Database\Converter\BooleanConverter(), false, true, false, NULL),

],
'bySqlName' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'configuration' => new Jinya\Database\Cache\CacheColumn('configuration', 'configuration', new Jinya\Cms\Database\Converter\JsonConverter(), false, true, false, NULL),
'description' => new Jinya\Database\Cache\CacheColumn('description', 'description', new Jinya\Cms\Database\Converter\ThemeDescriptionConverter(), false, true, false, NULL),
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'display_name' => new Jinya\Database\Cache\CacheColumn('displayName', 'display_name', null, false, true, false, NULL),
'scss_variables' => new Jinya\Database\Cache\CacheColumn('scssVariables', 'scss_variables', new Jinya\Cms\Database\Converter\JsonConverter(), false, true, false, NULL),
'has_api_theme' => new Jinya\Database\Cache\CacheColumn('hasApiTheme', 'has_api_theme', new Jinya\Cms\Database\Converter\BooleanConverter(), false, true, false, NULL),

],
];