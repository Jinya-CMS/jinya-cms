<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class BlogPostUpdated extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'blog-post-updated';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
alter table blog_post 
    add column last_updated_at datetime not null default now();
alter table blog_post
    add column updated_by_id int null;
alter table blog_post
    add constraint FK_users_blog_post_updated_by_id
        foreign key (updated_by_id) references users (id)
            on delete set null;
update blog_post set last_updated_at = created_at where updated_by_id is null;
update blog_post set updated_by_id = creator_id where updated_by_id is null;
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
