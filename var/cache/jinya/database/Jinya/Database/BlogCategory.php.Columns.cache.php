<?php
global $BlogCategory__Columns;

$BlogCategory__Columns = [
'byProperty' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'description' => new Jinya\Database\Cache\CacheColumn('description', 'description', null, false, false, false, NULL),
'parentId' => new Jinya\Database\Cache\CacheColumn('parentId', 'parent_id', null, false, false, false, NULL),
'webhookEnabled' => new Jinya\Database\Cache\CacheColumn('webhookEnabled', 'webhook_enabled', new Jinya\Cms\Database\Converter\BooleanConverter(), false, true, false, NULL),
'webhookUrl' => new Jinya\Database\Cache\CacheColumn('webhookUrl', 'webhook_url', null, false, false, false, NULL),

],
'bySqlName' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'description' => new Jinya\Database\Cache\CacheColumn('description', 'description', null, false, false, false, NULL),
'parent_id' => new Jinya\Database\Cache\CacheColumn('parentId', 'parent_id', null, false, false, false, NULL),
'webhook_enabled' => new Jinya\Database\Cache\CacheColumn('webhookEnabled', 'webhook_enabled', new Jinya\Cms\Database\Converter\BooleanConverter(), false, true, false, NULL),
'webhook_url' => new Jinya\Database\Cache\CacheColumn('webhookUrl', 'webhook_url', null, false, false, false, NULL),

],
];