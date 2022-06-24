<?php

namespace App\Tests\Extensions;

use App\Database\Utils\LoadableEntity;
use PHPUnit\Runner\AfterLastTestHook;

/**
 *
 */
class DropAllTablesHook implements AfterLastTestHook
{

    public function executeAfterLastTest(): void
    {
        LoadableEntity::executeSqlString('drop table if exists api_key');
        LoadableEntity::executeSqlString('drop table if exists blog_post_segment');
        LoadableEntity::executeSqlString('drop table if exists blog_post');
        LoadableEntity::executeSqlString('drop table if exists configuration');
        LoadableEntity::executeSqlString('drop table if exists form_item');
        LoadableEntity::executeSqlString('drop table if exists gallery_file_position');
        LoadableEntity::executeSqlString('drop table if exists known_device');
        LoadableEntity::executeSqlString('drop table if exists menu_item');
        LoadableEntity::executeSqlString('drop table if exists migration_state');
        LoadableEntity::executeSqlString('drop table if exists segment');
        LoadableEntity::executeSqlString('drop table if exists theme_asset');
        LoadableEntity::executeSqlString('drop table if exists theme_blog_category');
        LoadableEntity::executeSqlString('drop table if exists blog_category');
        LoadableEntity::executeSqlString('drop table if exists theme_file');
        LoadableEntity::executeSqlString('drop table if exists theme_form');
        LoadableEntity::executeSqlString('drop table if exists message');
        LoadableEntity::executeSqlString('drop table if exists form');
        LoadableEntity::executeSqlString('drop table if exists theme_gallery');
        LoadableEntity::executeSqlString('drop table if exists gallery');
        LoadableEntity::executeSqlString('drop table if exists theme_menu');
        LoadableEntity::executeSqlString('drop table if exists menu');
        LoadableEntity::executeSqlString('drop table if exists theme_page');
        LoadableEntity::executeSqlString('drop table if exists page');
        LoadableEntity::executeSqlString('drop table if exists theme_segment_page');
        LoadableEntity::executeSqlString('drop table if exists segment_page');
        LoadableEntity::executeSqlString('drop table if exists theme');
        LoadableEntity::executeSqlString('drop table if exists uploading_file_chunk');
        LoadableEntity::executeSqlString('drop table if exists uploading_file');
        LoadableEntity::executeSqlString('drop table if exists file');
        LoadableEntity::executeSqlString('drop table if exists users');
    }
}