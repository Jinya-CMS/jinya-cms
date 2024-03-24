<?php

const __ROOT__ = __DIR__;
const INSTALLED_VERSION = '%VERSION%';

const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';
const __JINYA_TEMP = __ROOT__ . '/tmp/';
const __JINYA_MODEL_NAMESPACE = 'App\Database\\';
if (!is_dir(__JINYA_TEMP) && !mkdir($concurrentDirectory = __JINYA_TEMP, 0775, true) && !is_dir($concurrentDirectory)) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
}
if (!is_dir(__ROOT__ . '/public/jinya-content') && !mkdir(
    $concurrentDirectory = __ROOT__ . '/public/jinya-content',
    0775,
    true
) && !is_dir(
    $concurrentDirectory
)) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
}
