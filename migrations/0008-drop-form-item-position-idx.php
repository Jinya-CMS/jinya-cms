<?php

return [
    'version' => 'drop-form-item-position-idx',
    'sql' => <<<'SQL'
alter table form_item
    drop key idx_form_item_position_form;
SQL
];