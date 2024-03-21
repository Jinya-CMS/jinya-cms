<?php

return [
    'version' => 'form-item-bool-columns',
    'sql' => <<<'SQL'
alter table form_item 
    modify is_from_address bool default false null;
alter table form_item 
    modify is_subject bool default false null;
alter table form_item 
    modify is_required bool default false null;
SQL
];
