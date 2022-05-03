<?php

const __ROOT__ = __DIR__;
const INSTALLED_VERSION = '%VERSION%';

const __JINYA_TEMP = __ROOT__ . '/tmp/';
const __JINYA_MODEL_NAMESPACE = 'App\Database\\';
if (!is_dir(__JINYA_TEMP)) {
    mkdir(__JINYA_TEMP, 0775, true);
}
if (!is_dir(__ROOT__ . '/public/jinya-content')) {
    mkdir(__ROOT__ . '/public/jinya-content', 0775, true);
}