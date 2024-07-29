<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class CollationUtf8Mb4 extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'collation-utf8mb4';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
alter table api_key convert to character set utf8mb4 collate utf8mb4_bin;
alter table blog_category convert to character set utf8mb4 collate utf8mb4_bin;
alter table blog_post convert to character set utf8mb4 collate utf8mb4_bin;
alter table blog_post_segment convert to character set utf8mb4 collate utf8mb4_bin;
alter table configuration convert to character set utf8mb4 collate utf8mb4_bin;
alter table file convert to character set utf8mb4 collate utf8mb4_bin;
alter table form convert to character set utf8mb4 collate utf8mb4_bin;
alter table form_item convert to character set utf8mb4 collate utf8mb4_bin;
alter table gallery convert to character set utf8mb4 collate utf8mb4_bin;
alter table gallery_file_position convert to character set utf8mb4 collate utf8mb4_bin;
alter table known_device convert to character set utf8mb4 collate utf8mb4_bin;
alter table menu convert to character set utf8mb4 collate utf8mb4_bin;
alter table menu_item convert to character set utf8mb4 collate utf8mb4_bin;
alter table migration_state convert to character set utf8mb4 collate utf8mb4_bin;
alter table page convert to character set utf8mb4 collate utf8mb4_bin;
alter table segment convert to character set utf8mb4 collate utf8mb4_bin;
alter table segment_page convert to character set utf8mb4 collate utf8mb4_bin;
alter table theme convert to character set utf8mb4 collate utf8mb4_bin;
alter table theme_asset convert to character set utf8mb4 collate utf8mb4_bin;
alter table theme_blog_category convert to character set utf8mb4 collate utf8mb4_bin;
alter table theme_file convert to character set utf8mb4 collate utf8mb4_bin;
alter table theme_form convert to character set utf8mb4 collate utf8mb4_bin;
alter table theme_gallery convert to character set utf8mb4 collate utf8mb4_bin;
alter table theme_menu convert to character set utf8mb4 collate utf8mb4_bin;
alter table theme_page convert to character set utf8mb4 collate utf8mb4_bin;
alter table theme_segment_page convert to character set utf8mb4 collate utf8mb4_bin;
SQL
        );
    }

    /**
     * @inheritDoc
     */
    public function down(PDO $pdo): void
    {
    }
}
