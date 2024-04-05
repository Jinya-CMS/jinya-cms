<?php
global $Menu__Columns;

$Menu__Columns = [
'byProperty' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'logo' => new Jinya\Database\Cache\CacheColumn('logo', 'logo', null, false, false, false, NULL),

],
'bySqlName' => [
'id' => new Jinya\Database\Cache\CacheColumn('id', 'id', null, true, true, false, NULL),
'name' => new Jinya\Database\Cache\CacheColumn('name', 'name', null, false, true, false, NULL),
'logo' => new Jinya\Database\Cache\CacheColumn('logo', 'logo', null, false, false, false, NULL),

],
];