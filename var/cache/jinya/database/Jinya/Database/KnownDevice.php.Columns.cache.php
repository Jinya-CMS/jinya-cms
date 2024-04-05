<?php
global $KnownDevice__Columns;

$KnownDevice__Columns = [
'byProperty' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'userId' => new Jinya\Database\Cache\CacheColumn('userId', 'user_id', null, false, true, false, NULL),
'deviceKey' => new Jinya\Database\Cache\CacheColumn('deviceKey', 'device_key', null, false, true, false, NULL),
'userAgent' => new Jinya\Database\Cache\CacheColumn('userAgent', 'user_agent', null, false, true, false, NULL),
'remoteAddress' => new Jinya\Database\Cache\CacheColumn('remoteAddress', 'remote_address', null, false, true, false, NULL),

],
'bySqlName' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'user_id' => new Jinya\Database\Cache\CacheColumn('userId', 'user_id', null, false, true, false, NULL),
'device_key' => new Jinya\Database\Cache\CacheColumn('deviceKey', 'device_key', null, false, true, false, NULL),
'user_agent' => new Jinya\Database\Cache\CacheColumn('userAgent', 'user_agent', null, false, true, false, NULL),
'remote_address' => new Jinya\Database\Cache\CacheColumn('remoteAddress', 'remote_address', null, false, true, false, NULL),

],
];