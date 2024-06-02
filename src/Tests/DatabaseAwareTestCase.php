<?php

namespace Jinya\Cms\Tests;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\Artist;
use Jinya\Cms\Database\Migrations\Migrator;
use Error;
use Jinya\Database\Entity;
use Jinya\Database\Exception\NotNullViolationException;
use PHPUnit\Framework\TestCase;

class DatabaseAwareTestCase extends TestCase
{
    public function expectError(): void
    {
        $this->expectException(Error::class);
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Migrator::migrate();
    }

    /**
     * @return void
     * @throws NotNullViolationException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->createArtist();
    }

    /**
     * @return void
     * @throws NotNullViolationException
     */
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
        Entity::getPDO()->exec('delete from theme_asset');
        Entity::getPDO()->exec('delete from theme_blog_category');
        Entity::getPDO()->exec('delete from theme_file');
        Entity::getPDO()->exec('delete from theme_form');
        Entity::getPDO()->exec('delete from theme_gallery');
        Entity::getPDO()->exec('delete from theme_menu');
        Entity::getPDO()->exec('delete from theme_page');
        Entity::getPDO()->exec('delete from theme_segment_page');
        Entity::getPDO()->exec('delete from configuration');

        Entity::getPDO()->exec('delete from form_item');
        Entity::getPDO()->exec('delete from gallery_file_position');

        Entity::getPDO()->exec('delete from uploading_file_chunk');
        Entity::getPDO()->exec('delete from uploading_file');

        Entity::getPDO()->exec('delete from file_tag_file');
        Entity::getPDO()->exec('delete from file_tag');
        Entity::getPDO()->exec('delete from file');

        Entity::getPDO()->exec('delete from known_device');
        Entity::getPDO()->exec('delete from api_key');

        Entity::getPDO()->exec('delete from blog_post_segment');
        Entity::getPDO()->exec('delete from segment');
        Entity::getPDO()->exec('delete from menu_item');

        Entity::getPDO()->exec('delete from blog_post');

        Entity::getPDO()->exec('delete from blog_category');

        Entity::getPDO()->exec('delete from segment_page');
        Entity::getPDO()->exec('delete from form');
        Entity::getPDO()->exec('delete from gallery');
        Entity::getPDO()->exec('delete from menu');
        Entity::getPDO()->exec('delete from page');
        Entity::getPDO()->exec('delete from theme');
        Entity::getPDO()->exec('delete from users');

        Entity::getPDO()->exec('delete from jinya_configuration');
    }
}
