<?php

namespace Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class Blog extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'blog';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
create table if not exists blog_category
(
	id int auto_increment primary key,
	name varchar(255) not null unique,
	description text null,
	parent_id int null,
	constraint FK_category_category_parent_id
		foreign key (parent_id) references blog_category (id)
			on delete set null
);

create table blog_post
(
	id int auto_increment primary key,
	title varchar(255) null unique,
	slug varchar(255) null unique,
	header_image_id int null,
	public bool not null default false,
	created_at datetime default NOW() not null,
	creator_id int null,
	category_id int null,
	constraint FK_blog_post_category_category_id
		foreign key (category_id) references blog_category (id)
			on delete set null,
	constraint FK_blog_post_users_creator_id
		foreign key (creator_id) references users (id)
			on delete cascade,
	constraint FK_blog_post_file_header_image_id
		foreign key (header_image_id) references file (id)
			on delete set null
);

create table blog_post_segment
(
	id int auto_increment primary key,
	gallery_id int null,
	html longtext null,
	file_id int null,
	blog_post_id int not null,
	link varchar(255) null,
	position int not null,
	constraint FK_blog_post_segment_file_file_id
		foreign key (file_id) references file (id)
			on delete cascade,
	constraint FK_blog_post_segment_gallery_gallery_id
		foreign key (gallery_id) references gallery (id)
			on delete cascade,
	constraint FK_blog_post_segment_blog_post_blog_post_id
		foreign key (blog_post_id) references blog_post (id)
			on delete cascade
);

alter table menu_item
	add category_id int null;

alter table menu_item
	add constraint FK_category_menu_item
		foreign key (category_id) references blog_category (id)
			on delete cascade;

alter table menu_item
	add blog_home_page bool null;

create table theme_blog_category
(
	name varchar(255) not null,
	theme_id int not null,
	blog_category_id int not null,
	constraint PK_theme_blog_category 
	    primary key (name, theme_id,  blog_category_id),
    constraint FK_blog_category_theme_blog_category 
        foreign key (blog_category_id) references blog_category (id)
            on delete cascade,
    constraint FK_theme_theme_blog_category
        foreign key (theme_id) references theme (id)
            on delete cascade
);
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
