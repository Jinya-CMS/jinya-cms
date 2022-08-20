<?php

return [
    'version' => 'form-item-remove-unique-position',
    'sql' => <<<'SQL'
alter table form_item
    drop key idx_form_item_position_form;
alter table form_item
    drop column history;
SQL
];