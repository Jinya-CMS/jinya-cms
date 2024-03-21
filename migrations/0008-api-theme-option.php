<?php

return [
    'version' => 'api-theme-option',
    'sql' => <<<'SQL'
alter table theme
    add column has_api_theme bool not null default false;
SQL
];
