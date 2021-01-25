<?php

return [
    'version' => 'brute-force-prevention',
    'sql' => <<<'SQL'
alter table users
	add failed_login_attempts datetime null,
	add login_blocked_until datetime null;
SQL,
];
