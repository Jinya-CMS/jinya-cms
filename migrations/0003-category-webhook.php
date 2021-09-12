<?php

return [
    'version' => 'category-webhook',
    'sql' => <<<'SQL'
alter table blog_category
	add webhook_url varchar(255) null;

alter table blog_category
	add webhook_enabled bool null default false;
SQL
];