<?php

return [
    'version' => 'artist-dark-light-switch',
    'sql' => <<<'SQL'
alter table users
    add column prefers_color_scheme bool null default null;
SQL
];