<?php

namespace App\Tests;

use App\Authentication\CurrentUser;
use App\Database\Artist;
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
        Entity::getPDO()->exec('DELETE FROM theme_blog_category');
        Entity::getPDO()->exec('DELETE FROM blog_post_segment');
        Entity::getPDO()->exec('DELETE FROM blog_post');
        Entity::getPDO()->exec('DELETE FROM blog_category');
        Entity::getPDO()->exec('DELETE FROM configuration');
        Entity::getPDO()->exec('DELETE FROM form_item');
        Entity::getPDO()->exec('DELETE FROM gallery_file_position');
        Entity::getPDO()->exec('DELETE FROM known_device');
        Entity::getPDO()->exec('DELETE FROM menu_item');
        Entity::getPDO()->exec('DELETE FROM segment');
        Entity::getPDO()->exec('DELETE FROM theme_asset');
        Entity::getPDO()->exec('DELETE FROM theme_file');
        Entity::getPDO()->exec('DELETE FROM theme_form');
        Entity::getPDO()->exec('DELETE FROM form');
        Entity::getPDO()->exec('DELETE FROM theme_gallery');
        Entity::getPDO()->exec('DELETE FROM gallery');
        Entity::getPDO()->exec('DELETE FROM theme_menu');
        Entity::getPDO()->exec('DELETE FROM menu');
        Entity::getPDO()->exec('DELETE FROM theme_page');
        Entity::getPDO()->exec('DELETE FROM page');
        Entity::getPDO()->exec('DELETE FROM theme_segment_page');
        Entity::getPDO()->exec('DELETE FROM segment_page');
        Entity::getPDO()->exec('DELETE FROM theme');
        Entity::getPDO()->exec('DELETE FROM uploading_file_chunk');
        Entity::getPDO()->exec('DELETE FROM uploading_file');
        Entity::getPDO()->exec('DELETE FROM file_tag_file');
        Entity::getPDO()->exec('DELETE FROM file_tag');
        Entity::getPDO()->exec('DELETE FROM file');
        Entity::getPDO()->exec('DELETE FROM users');
    }
}
