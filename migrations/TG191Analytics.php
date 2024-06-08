<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class TG191Analytics extends AbstractMigration
{
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<SQL
create table analytics (
    id int primary key auto_increment,
    route varchar(255) not null,
    timestamp date default current_date() not null,
    unique_visit bool default false not null,
    operating_system varchar(255) null,
    operating_system_version varchar(255) null,
    browser varchar(255) null,
    browser_version varchar(255) null,
    device_type tinyint null,
    entity_id int null,
    entity_type int null,
    language varchar(255) null,
    device varchar(255) null,
    brand varchar(255) null,
    country char(2) null,
    user_agent varchar(255) null,
    referer text null,
    status smallint not null default 200,
    
    index (unique_visit),
    index (timestamp),
    index (route),
    index (entity_id, entity_type)
);
SQL
        );
    }

    public function down(PDO $pdo): void
    {
    }
}
