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
        LoadableEntity::executeSqlString('drop table api_key');
        LoadableEntity::executeSqlString('drop table blog_post_segment');
        LoadableEntity::executeSqlString('drop table blog_post');
        LoadableEntity::executeSqlString('drop table configuration');
        LoadableEntity::executeSqlString('drop table form_item');
        LoadableEntity::executeSqlString('drop table gallery_file_position');
        LoadableEntity::executeSqlString('drop table known_device');
        LoadableEntity::executeSqlString('drop table menu_item');
        LoadableEntity::executeSqlString('drop table message');
        LoadableEntity::executeSqlString('drop table migration_state');
        LoadableEntity::executeSqlString('drop table segment');
        LoadableEntity::executeSqlString('drop table theme_asset');
        LoadableEntity::executeSqlString('drop table theme_blog_category');
        LoadableEntity::executeSqlString('drop table blog_category');
        LoadableEntity::executeSqlString('drop table theme_file');
        LoadableEntity::executeSqlString('drop table theme_form');
        LoadableEntity::executeSqlString('drop table form');
        LoadableEntity::executeSqlString('drop table theme_gallery');
        LoadableEntity::executeSqlString('drop table gallery');
        LoadableEntity::executeSqlString('drop table theme_menu');
        LoadableEntity::executeSqlString('drop table menu');
        LoadableEntity::executeSqlString('drop table theme_page');
        LoadableEntity::executeSqlString('drop table page');
        LoadableEntity::executeSqlString('drop table theme_segment_page');
        LoadableEntity::executeSqlString('drop table segment_page');
        LoadableEntity::executeSqlString('drop table theme');
        LoadableEntity::executeSqlString('drop table uploading_file_chunk');
        LoadableEntity::executeSqlString('drop table uploading_file');
        LoadableEntity::executeSqlString('drop table file');
        LoadableEntity::executeSqlString('drop table users');
    }
}