<?php

const __ROOT__ = __DIR__;
const INSTALLED_VERSION = '%VERSION%';

const __JINYA_TEMP = __ROOT__ . '/tmp/';
if (!is_dir(__JINYA_TEMP)) {
    mkdir(__JINYA_TEMP, 0775, true);
}