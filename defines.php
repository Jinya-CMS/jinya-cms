<?php

const __ROOT__ = __DIR__;
const INSTALLED_VERSION = '%VERSION%';

const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';

/** @var string Users in this role are allowed to see content but not edit it */
const ROLE_READER = 'ROLE_READER';
/** @var string Users in this role are allowed to see and edit content but not administer Jinya CMS */
const ROLE_WRITER = 'ROLE_WRITER';
/** @var string Users in this role are allowed to see and edit content and to administer Jinya CMS */
const ROLE_ADMIN = 'ROLE_ADMIN';

const __JINYA_LOGS = __DIR__ . '/logs/';
const __JINYA_TEMP = __DIR__ . '/tmp/';
const __JINYA_CACHE = __DIR__ . '/var/cache';
const __JINYA_ENTITY = __DIR__ . '/src/Database';
const __JINYA_CONTROLLERS = __DIR__ . '/src/Web/Controllers';

if (!is_dir(__JINYA_TEMP) && !mkdir($concurrentDirectory = __JINYA_TEMP, 0775, true) && !is_dir($concurrentDirectory)) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
}
