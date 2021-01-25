<?php

return [
    'version' => 'brute-force-prevention',
    'sql' => <<<'SQL'
ALTER TABLE users ADD COLUMN failed_login_attempts int NULL;
ALTER TABLE users ADD COLUMN login_blocked_until datetime NULL;
SQL,
];
