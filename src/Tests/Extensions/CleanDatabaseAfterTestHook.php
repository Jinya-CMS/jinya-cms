<?php

namespace App\Tests\Extensions;

use App\Database\Utils\LoadableEntity;
use PHPUnit\Runner\AfterTestHook;

/**
 *
 */
class CleanDatabaseAfterTestHook implements AfterTestHook
{

    /**
     * @inheritDoc
     */
    public function executeAfterTest(string $test, float $time): void
    {
        LoadableEntity::executeSqlString('DELETE FROM theme_blog_category');
        LoadableEntity::executeSqlString('DELETE FROM blog_post_segment');
        LoadableEntity::executeSqlString('DELETE FROM blog_post');
        LoadableEntity::executeSqlString('DELETE FROM blog_category');
        LoadableEntity::executeSqlString('DELETE FROM configuration');
        LoadableEntity::executeSqlString('DELETE FROM form_item');
        LoadableEntity::executeSqlString('DELETE FROM gallery_file_position');
        LoadableEntity::executeSqlString('DELETE FROM known_device');
        LoadableEntity::executeSqlString('DELETE FROM menu_item');
        LoadableEntity::executeSqlString('DELETE FROM segment');
        LoadableEntity::executeSqlString('DELETE FROM theme_asset');
        LoadableEntity::executeSqlString('DELETE FROM theme_file');
        LoadableEntity::executeSqlString('DELETE FROM theme_form');
        LoadableEntity::executeSqlString('DELETE FROM form');
        LoadableEntity::executeSqlString('DELETE FROM theme_gallery');
        LoadableEntity::executeSqlString('DELETE FROM gallery');
        LoadableEntity::executeSqlString('DELETE FROM theme_menu');
        LoadableEntity::executeSqlString('DELETE FROM menu');
        LoadableEntity::executeSqlString('DELETE FROM theme_page');
        LoadableEntity::executeSqlString('DELETE FROM page');
        LoadableEntity::executeSqlString('DELETE FROM theme_segment_page');
        LoadableEntity::executeSqlString('DELETE FROM segment_page');
        LoadableEntity::executeSqlString('DELETE FROM theme');
        LoadableEntity::executeSqlString('DELETE FROM uploading_file_chunk');
        LoadableEntity::executeSqlString('DELETE FROM uploading_file');
        LoadableEntity::executeSqlString('DELETE FROM file');
        LoadableEntity::executeSqlString('DELETE FROM users');
    }
}