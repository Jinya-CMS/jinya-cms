<?php
global $ClassicPage__Columns;

$ClassicPage__Columns = [
'byProperty' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'creatorId' => new Jinya\Database\Cache\CacheColumn('creatorId', 'creator_id', null, false, true, false, NULL),
'updatedById' => new Jinya\Database\Cache\CacheColumn('updatedById', 'updated_by_id', null, false, true, false, NULL),
'createdAt' => new Jinya\Database\Cache\CacheColumn('createdAt', 'created_at', new Jinya\Database\Converters\DateConverter('Y-m-d H:i:s'), false, true, false, NULL),
'lastUpdatedAt' => new Jinya\Database\Cache\CacheColumn('lastUpdatedAt', 'last_updated_at', new Jinya\Database\Converters\DateConverter('Y-m-d H:i:s'), false, true, false, NULL),
'content' => new Jinya\Database\Cache\CacheColumn('content', 'content', null, false, true, false, NULL),
'title' => new Jinya\Database\Cache\CacheColumn('title', 'title', null, false, true, false, NULL),

],
'bySqlName' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'creator_id' => new Jinya\Database\Cache\CacheColumn('creatorId', 'creator_id', null, false, true, false, NULL),
'updated_by_id' => new Jinya\Database\Cache\CacheColumn('updatedById', 'updated_by_id', null, false, true, false, NULL),
'created_at' => new Jinya\Database\Cache\CacheColumn('createdAt', 'created_at', new Jinya\Database\Converters\DateConverter('Y-m-d H:i:s'), false, true, false, NULL),
'last_updated_at' => new Jinya\Database\Cache\CacheColumn('lastUpdatedAt', 'last_updated_at', new Jinya\Database\Converters\DateConverter('Y-m-d H:i:s'), false, true, false, NULL),
'content' => new Jinya\Database\Cache\CacheColumn('content', 'content', null, false, true, false, NULL),
'title' => new Jinya\Database\Cache\CacheColumn('title', 'title', null, false, true, false, NULL),

],
];