<?php

namespace App\Tests;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Database\Utils\LoadableEntity;
use Error;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../defines.php';

class DatabaseAwareTestCase extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    public function expectError(): void
    {
        $this->expectException(Error::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createArtist();
    }

    private function createArtist(): void
    {
        $artist = new Artist();
        $artist->email = 'firstuser@example.com';
        $artist->aboutMe = 'About me';
        $artist->profilePicture = 'profilepicture';
        $artist->artistName = 'First user';
        $artist->enabled = true;
        $artist->roles = [];
        $artist->setPassword('start1234');
        $artist->roles[] = 'ROLE_READER';
        $artist->roles[] = 'ROLE_WRITER';

        $artist->create();
        CurrentUser::$currentUser = $artist;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanDatabase();
    }

    private function cleanDatabase(): void
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
        LoadableEntity::executeSqlString('DELETE FROM file_tag_file');
        LoadableEntity::executeSqlString('DELETE FROM file_tag');
        LoadableEntity::executeSqlString('DELETE FROM file');
        LoadableEntity::executeSqlString('DELETE FROM users');
    }
}
