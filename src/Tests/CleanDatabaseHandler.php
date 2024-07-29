<?php

namespace Jinya\Cms\Tests;

use Jinya\Database\Entity;
use PHPUnit\Event\Test\BeforeFirstTestMethodCalledSubscriber;
use PHPUnit\Event\Test\BeforeFirstTestMethodCalled;

class CleanDatabaseHandler implements BeforeFirstTestMethodCalledSubscriber
{
    public function notify(BeforeFirstTestMethodCalled $event): void
    {
        Entity::getPDO()->exec('drop table if exists theme_asset cascade');
        Entity::getPDO()->exec('drop table if exists theme_blog_category cascade');
        Entity::getPDO()->exec('drop table if exists theme_file cascade');
        Entity::getPDO()->exec('drop table if exists theme_form cascade');
        Entity::getPDO()->exec('drop table if exists theme_gallery cascade');
        Entity::getPDO()->exec('drop table if exists theme_menu cascade');
        Entity::getPDO()->exec('drop table if exists theme_page cascade');
        Entity::getPDO()->exec('drop table if exists theme_segment_page cascade');
        Entity::getPDO()->exec('drop table if exists configuration cascade');
        Entity::getPDO()->exec('drop table if exists form_item cascade');
        Entity::getPDO()->exec('drop table if exists gallery_file_position cascade');
        Entity::getPDO()->exec('drop table if exists uploading_file_chunk cascade');
        Entity::getPDO()->exec('drop table if exists uploading_file cascade');
        Entity::getPDO()->exec('drop table if exists file_tag_file cascade');
        Entity::getPDO()->exec('drop table if exists file_tag cascade');
        Entity::getPDO()->exec('drop table if exists known_device cascade');
        Entity::getPDO()->exec('drop table if exists api_key cascade');
        Entity::getPDO()->exec('drop table if exists blog_post_segment cascade');
        Entity::getPDO()->exec('drop table if exists segment cascade');
        Entity::getPDO()->exec('drop table if exists menu_item cascade');
        Entity::getPDO()->exec('drop table if exists blog_post cascade');
        Entity::getPDO()->exec('drop table if exists blog_category cascade');
        Entity::getPDO()->exec('drop table if exists segment_page cascade');
        Entity::getPDO()->exec('drop table if exists gallery cascade');
        Entity::getPDO()->exec('drop table if exists menu cascade');
        Entity::getPDO()->exec('drop table if exists page cascade');
        Entity::getPDO()->exec('drop table if exists theme cascade');
        Entity::getPDO()->exec('drop table if exists jinya_configuration cascade');
        Entity::getPDO()->exec('drop table if exists message cascade');
        Entity::getPDO()->exec('drop table if exists form cascade');
        Entity::getPDO()->exec('drop table if exists file cascade');
        Entity::getPDO()->exec('drop table if exists users cascade');
        Entity::getPDO()->exec('drop table if exists ip_address cascade');
        Entity::getPDO()->exec('drop table if exists migration_state cascade');
        Entity::getPDO()->exec('drop table if exists analytics cascade');
        Entity::getPDO()->exec('drop table if exists folder cascade');
    }
}
