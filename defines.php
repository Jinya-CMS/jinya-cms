<?php

const __ROOT__ = __DIR__;
const INSTALLED_VERSION = '20.0.10';
const __JINYA_TEMP = __ROOT__ . '/tmp/';
if (!is_dir(__JINYA_TEMP)) {
    mkdir(__JINYA_TEMP, true);
}