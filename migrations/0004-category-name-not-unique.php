<?php

return [
    'version' => 'category-name-not-unique',
    'sql' => <<<'SQL'
drop index name on blog_category;
SQL
];
