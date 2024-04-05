<?php
global $ApiKey__Columns;

$ApiKey__Columns = [
'byProperty' => [
'apiKey' => new Jinya\Database\Cache\CacheColumn('apiKey', 'api_key', null, false, true, true, NULL),
'userId' => new Jinya\Database\Cache\CacheColumn('userId', 'user_id', null, false, true, false, NULL),
'validSince' => new Jinya\Database\Cache\CacheColumn('validSince', 'valid_since', new Jinya\Database\Converters\DateConverter('Y-m-d H:i:s'), false, true, false, NULL),
'userAgent' => new Jinya\Database\Cache\CacheColumn('userAgent', 'user_agent', null, false, true, false, NULL),
'remoteAddress' => new Jinya\Database\Cache\CacheColumn('remoteAddress', 'remote_address', null, false, true, false, NULL),

],
'bySqlName' => [
'api_key' => new Jinya\Database\Cache\CacheColumn('apiKey', 'api_key', null, false, true, true, NULL),
'user_id' => new Jinya\Database\Cache\CacheColumn('userId', 'user_id', null, false, true, false, NULL),
'valid_since' => new Jinya\Database\Cache\CacheColumn('validSince', 'valid_since', new Jinya\Database\Converters\DateConverter('Y-m-d H:i:s'), false, true, false, NULL),
'user_agent' => new Jinya\Database\Cache\CacheColumn('userAgent', 'user_agent', null, false, true, false, NULL),
'remote_address' => new Jinya\Database\Cache\CacheColumn('remoteAddress', 'remote_address', null, false, true, false, NULL),

],
];