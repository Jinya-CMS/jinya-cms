<?php

namespace Jinya\Cms\Migrations;

use Jinya\Cms\Locate\IpToLocationService;
use Jinya\Database\Migration\AbstractMigration;
use PDO;

class TG217IpDatabase extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<SQL
create table ip_address (
  address_type enum('ipv4', 'ipv6') not null,
  ip_start varbinary(16) not null,
  ip_end varbinary(16) not null,
  country char(2) not null,
  primary key (address_type, ip_start),
  index (ip_start, ip_end)
);

insert into jinya_configuration (`key`, `group`, value, type) values ('ip_database_url', 'jinya', 'https://download.db-ip.com/free/dbip-country-lite-{YEAR}-{MONTH}.csv.gz', 0);
SQL
        );

        $ipToLocationService = new IpToLocationService();
        $ipToLocationService->populateDatabase();
    }

    /**
     * @inheritDoc
     */
    public function down(PDO $pdo): void
    {
    }
}
