<?php

namespace App\Database\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class InitialMigration extends AbstractMigration
{
    public function getMigrationName(): string
    {
        return 'initial';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<SQL
create table if not exists theme
(
 id int auto_increment
  primary key,
 configuration longtext not null,
 description varchar(255) not null,
 name varchar(255) not null,
 display_name varchar(255) not null,
 scss_variables longtext not null
)
collate=utf8_unicode_ci;

create table if not exists configuration
(
 id int auto_increment
  primary key,
 current_frontend_theme_id int null,
 constraint UNIQ_configuration_current_frontend_theme_id
  unique (current_frontend_theme_id),
 constraint FK_theme_configuration
  foreign key (current_frontend_theme_id) references theme (id)
)
collate=utf8_unicode_ci;

insert into configuration (current_frontend_theme_id) values (null); 

create table if not exists theme_asset
(
 theme_id int not null,
 name varchar(255) not null,
 public_path varchar(255) not null,
 constraint FK_theme_asset_theme
  foreign key (theme_id) references theme (id)
   on delete cascade
);

create table if not exists users
(
 id int auto_increment
  primary key,
 email varchar(255) not null,
 enabled tinyint(1) not null,
 two_factor_token varchar(255) null,
 password longtext not null,
 last_login datetime null,
 confirmation_token varchar(255) null,
 password_requested_at datetime null,
 roles longtext not null,
 artist_name varchar(255) not null,
 profile_picture varchar(255) null,
 about_me text null,
 constraint UNIQ_users_email
  unique (email)
)
collate=utf8_unicode_ci;

create table if not exists api_key
(
 api_key varchar(255) not null
  primary key,
 user_id int null,
 valid_since datetime not null,
 user_agent varchar(255) null,
 remote_address varchar(255) null,
 constraint FK_users_api_key
  foreign key (user_id) references users (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_C912ED9DA76ED395
 on api_key (user_id);

create table if not exists file
(
 id int auto_increment
  primary key,
 creator_id int null,
 updated_by_id int null,
 created_at datetime not null,
 last_updated_at datetime not null,
 path varchar(255) not null,
 name varchar(255) not null,
 type varchar(255) not null,
 constraint UNIQ_file_name
  unique (name),
 constraint FK_users_file_creator_id
  foreign key (creator_id) references users (id)
   on delete set null,
 constraint FK_users_file_updated_by_id
  foreign key (updated_by_id) references users (id)
   on delete set null
)
collate=utf8_unicode_ci;

create index IDX_8C9F361061220EA6
 on file (creator_id);

create index IDX_8C9F3610896DBBDE
 on file (updated_by_id);

create table if not exists form
(
 id int auto_increment
  primary key,
 creator_id int null,
 updated_by_id int null,
 created_at datetime not null,
 last_updated_at datetime not null,
 to_address varchar(255) not null,
 title varchar(255) not null,
 description longtext not null,
 constraint UNIQ_form_title
  unique (title),
 constraint FK_users_form_creator_id
  foreign key (creator_id) references users (id)
   on delete set null,
 constraint FK_users_form_last_updated_by_id
  foreign key (updated_by_id) references users (id)
   on delete set null
)
collate=utf8_unicode_ci;

create index IDX_5288FD4F61220EA6
 on form (creator_id);

create index IDX_5288FD4F896DBBDE
 on form (updated_by_id);

create table if not exists form_item
(
 id int auto_increment
  primary key,
 form_id int null,
 type varchar(255) not null,
 options longtext not null,
 spam_filter longtext null,
 label varchar(255) not null,
 help_text varchar(255) not null,
 position int not null,
 is_from_address bit default b'0' null,
 is_subject bit default b'0' null,
 is_required bit default b'0' null,
 placeholder varchar(255) null,
 constraint FK_form_form_item
  foreign key (form_id) references form (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_8B3A21095FF69B7D
 on form_item (form_id);

create table if not exists gallery
(
 id int auto_increment
  primary key,
 creator_id int null,
 updated_by_id int null,
 created_at datetime not null,
 last_updated_at datetime not null,
 name varchar(255) not null,
 description varchar(255) not null,
 type varchar(255) not null,
 orientation varchar(255) not null,
 constraint UNIQ_gallery_name
  unique (name),
 constraint FK_users_gallery_creator_id
  foreign key (creator_id) references users (id)
   on delete set null,
 constraint FK_users_gallery_last_updated_by_id
  foreign key (updated_by_id) references users (id)
)
collate=utf8_unicode_ci;

create index IDX_472B783A61220EA6
 on gallery (creator_id);

create index IDX_472B783A896DBBDE
 on gallery (updated_by_id);

create table if not exists gallery_file_position
(
 id int auto_increment
  primary key,
 gallery_id int null,
 file_id int null,
 position int not null,
 constraint FK_file_gallery_file_position
  foreign key (file_id) references file (id)
   on delete cascade,
 constraint FK_gallery_gallery_file_position
  foreign key (gallery_id) references gallery (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_57EA691A4E7AF8F
 on gallery_file_position (gallery_id);

create index IDX_57EA691A93CB796C
 on gallery_file_position (file_id);

create table if not exists known_device
(
 id int auto_increment
  primary key,
 user_id int null,
 device_key varchar(255) not null,
 user_agent varchar(255) not null,
 remote_address varchar(255) not null,
 constraint FK_users_known_device
  foreign key (user_id) references users (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_3C887E4CA76ED395
 on known_device (user_id);

create table if not exists menu
(
 id int auto_increment
  primary key,
 name varchar(255) not null,
 logo int null,
 constraint FK_file_menu
  foreign key (logo) references file (id)
   on delete set null
)
collate=utf8_unicode_ci;

create table if not exists message
(
 id int auto_increment
  primary key,
 form_id int null,
 subject varchar(255) not null,
 content longtext not null,
 from_address varchar(255) not null,
 spam tinyint(1) not null,
 target_address varchar(255) not null,
 send_at datetime not null,
 is_archived tinyint(1) not null,
 is_deleted tinyint(1) not null,
 is_read tinyint(1) not null,
 answer longtext null,
 constraint FK_form_message
  foreign key (form_id) references form (id)
)
collate=utf8_unicode_ci;

create index IDX_B6BD307F5FF69B7D
 on message (form_id);

create table if not exists page
(
 id int auto_increment
  primary key,
 creator_id int null,
 updated_by_id int null,
 created_at datetime not null,
 last_updated_at datetime not null,
 content longtext not null,
 title varchar(255) not null,
 constraint UNIQ_page_title
  unique (title),
 constraint FK_users_page_creator_id
  foreign key (creator_id) references users (id)
   on delete set null,
 constraint FK_users_page_last_updated_by_id
  foreign key (updated_by_id) references users (id)
   on delete set null
)
collate=utf8_unicode_ci;

create index IDX_140AB62061220EA6
 on page (creator_id);

create index IDX_140AB620896DBBDE
 on page (updated_by_id);

create table if not exists segment_page
(
 id int auto_increment
  primary key,
 creator_id int null,
 updated_by_id int null,
 created_at datetime not null,
 last_updated_at datetime not null,
 name varchar(255) not null,
 constraint UNIQ_segment_page_name
  unique (name),
 constraint FK_users_segment_page_creator_id
  foreign key (creator_id) references users (id)
   on delete set null,
 constraint FK_users_segment_page_last_updated_by_id
  foreign key (updated_by_id) references users (id)
   on delete set null
)
collate=utf8_unicode_ci;

create table if not exists menu_item
(
 id int auto_increment
  primary key,
 menu_id int null,
 parent_id int null,
 title varchar(255) not null,
 highlighted tinyint(1) not null,
 position int not null,
 artist_id int null,
 page_id int null,
 form_id int null,
 gallery_id int null,
 segment_page_id int null,
 route varchar(255) null,
 constraint FK_artist_menu_item
  foreign key (artist_id) references users (id)
   on delete cascade,
 constraint FK_form_menu_item
  foreign key (form_id) references form (id)
   on delete cascade,
 constraint FK_gallery_menu_item
  foreign key (gallery_id) references gallery (id)
   on delete cascade,
 constraint FK_menu_item_menu_item_parent
  foreign key (parent_id) references menu_item (id)
   on delete cascade,
 constraint FK_menu_menu_item
  foreign key (menu_id) references menu (id)
   on delete cascade,
 constraint FK_page_menu_item
  foreign key (page_id) references page (id)
   on delete cascade,
 constraint FK_segment_page_menu_item
  foreign key (segment_page_id) references segment_page (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_D754D550727ACA70
 on menu_item (parent_id);

create index IDX_D754D550CCD7E912
 on menu_item (menu_id);

create table if not exists segment
(
 id int auto_increment
  primary key,
 page_id int null,
 form_id int null,
 gallery_id int null,
 file_id int null,
 position int not null,
 html text null,
 action longtext null,
 script varchar(255) null,
 target varchar(255) null,
 constraint FK_file_segment
  foreign key (file_id) references file (id)
   on delete cascade,
 constraint FK_form_segment
  foreign key (form_id) references form (id)
   on delete cascade,
 constraint FK_gallery_segment
  foreign key (gallery_id) references gallery (id)
   on delete cascade,
 constraint FK_segment_page_segment
  foreign key (page_id) references segment_page (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_1881F5654E7AF8F
 on segment (gallery_id);

create index IDX_1881F5655FF69B7D
 on segment (form_id);

create index IDX_1881F56593CB796C
 on segment (file_id);

create index IDX_1881F565C4663E4
 on segment (page_id);

create index IDX_CBA9317D61220EA6
 on segment_page (creator_id);

create index IDX_CBA9317D896DBBDE
 on segment_page (updated_by_id);

create table if not exists theme_file
(
 name varchar(255) not null,
 theme_id int not null,
 file_id int not null,
 primary key (theme_id, file_id, name),
 constraint FK_file_theme_file
  foreign key (file_id) references file (id)
   on delete cascade,
 constraint FK_theme_theme_file
  foreign key (theme_id) references theme (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_C658C22D59027487
 on theme_file (theme_id);

create index IDX_C658C22D93CB796C
 on theme_file (file_id);

create table if not exists theme_form
(
 name varchar(255) not null,
 theme_id int not null,
 form_id int not null,
 primary key (theme_id, form_id, name),
 constraint FK_form_theme_form
  foreign key (form_id) references form (id)
   on delete cascade,
 constraint FK_theme_theme_form
  foreign key (theme_id) references theme (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_184F097259027487
 on theme_form (theme_id);

create index IDX_184F09725FF69B7D
 on theme_form (form_id);

create table if not exists theme_gallery
(
 name varchar(255) not null,
 theme_id int not null,
 gallery_id int not null,
 primary key (theme_id, gallery_id, name),
 constraint FK_gallery_theme_gallery
  foreign key (gallery_id) references gallery (id)
   on delete cascade,
 constraint FK_theme_theme_gallery
  foreign key (theme_id) references theme (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_1E487D194E7AF8F
 on theme_gallery (gallery_id);

create index IDX_1E487D1959027487
 on theme_gallery (theme_id);

create table if not exists theme_menu
(
 name varchar(255) not null,
 theme_id int not null,
 menu_id int not null,
 primary key (theme_id, menu_id, name),
 constraint FK_menu_theme_menu
  foreign key (menu_id) references menu (id)
   on delete cascade,
 constraint FK_theme_theme_menu
  foreign key (theme_id) references theme (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_37C2CEAE59027487
 on theme_menu (theme_id);

create index IDX_37C2CEAECCD7E912
 on theme_menu (menu_id);

create table if not exists theme_page
(
 name varchar(255) not null,
 theme_id int not null,
 page_id int not null,
 primary key (theme_id, page_id, name),
 constraint FK_page_theme_page
  foreign key (page_id) references page (id)
   on delete cascade,
 constraint FK_theme_theme_page
  foreign key (theme_id) references theme (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_5ECD421D59027487
 on theme_page (theme_id);

create index IDX_5ECD421DC4663E4
 on theme_page (page_id);

create table if not exists theme_segment_page
(
 name varchar(255) not null,
 theme_id int not null,
 segment_page_id int not null,
 primary key (theme_id, segment_page_id, name),
 constraint FK_segment_page_theme_segment_page
  foreign key (segment_page_id) references segment_page (id)
   on delete cascade,
 constraint FK_theme_theme_segment_page
  foreign key (theme_id) references theme (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_5BFE07FF533F2206
 on theme_segment_page (segment_page_id);

create index IDX_5BFE07FF59027487
 on theme_segment_page (theme_id);

create table if not exists uploading_file
(
 id char(36) not null
  primary key,
 file_id int null,
 constraint UNIQ_C219262693CB796C
  unique (file_id),
 constraint FK_uploading_file_file
  foreign key (file_id) references file (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create table if not exists uploading_file_chunk
(
 id int auto_increment
  primary key,
 uploading_file_id char(36) null,
 chunk_path varchar(255) not null,
 chunk_position int not null,
 constraint FK_uploading_file_uploading_file_chunk
  foreign key (uploading_file_id) references uploading_file (id)
   on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_3F70FB06FD34D444
 on uploading_file_chunk (uploading_file_id);
SQL
        );
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function down(PDO $pdo): void
    {
    }
}
