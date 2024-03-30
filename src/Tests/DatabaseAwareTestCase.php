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

    /**
     * @return void
     * @throws NotNullViolationException
     */
    protected function setUp(): void
    {
        parent::setUp();
        Migrator::migrate();
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
        Entity::getPDO()->exec('drop table if exists api_key cascade');
        Entity::getPDO()->exec('drop table if exists blog_post_segment cascade');
        Entity::getPDO()->exec('drop table if exists blog_post cascade');
        Entity::getPDO()->exec('drop table if exists configuration cascade');
        Entity::getPDO()->exec('drop table if exists form_item cascade');
        Entity::getPDO()->exec('drop table if exists gallery_file_position cascade');
        Entity::getPDO()->exec('drop table if exists known_device cascade');
        Entity::getPDO()->exec('drop table if exists menu_item cascade');
        Entity::getPDO()->exec('drop table if exists migration_state cascade');
        Entity::getPDO()->exec('drop table if exists segment cascade');
        Entity::getPDO()->exec('drop table if exists theme_asset cascade');
        Entity::getPDO()->exec('drop table if exists theme_blog_category cascade');
        Entity::getPDO()->exec('drop table if exists blog_category cascade');
        Entity::getPDO()->exec('drop table if exists theme_file cascade');
        Entity::getPDO()->exec('drop table if exists theme_form cascade');
        Entity::getPDO()->exec('drop table if exists message cascade');
        Entity::getPDO()->exec('drop table if exists form cascade');
        Entity::getPDO()->exec('drop table if exists theme_gallery cascade');
        Entity::getPDO()->exec('drop table if exists gallery cascade');
        Entity::getPDO()->exec('drop table if exists theme_menu cascade');
        Entity::getPDO()->exec('drop table if exists menu cascade');
        Entity::getPDO()->exec('drop table if exists theme_page cascade');
        Entity::getPDO()->exec('drop table if exists page cascade');
        Entity::getPDO()->exec('drop table if exists theme_segment_page cascade');
        Entity::getPDO()->exec('drop table if exists segment_page cascade');
        Entity::getPDO()->exec('drop table if exists theme cascade');
        Entity::getPDO()->exec('drop table if exists uploading_file_chunk cascade');
        Entity::getPDO()->exec('drop table if exists uploading_file cascade');
        Entity::getPDO()->exec('drop table if exists file_tag_file cascade');
        Entity::getPDO()->exec('drop table if exists file_tag cascade');
        Entity::getPDO()->exec('drop table if exists file cascade');
        Entity::getPDO()->exec('drop table if exists users cascade');
    }
}
