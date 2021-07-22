<?php

namespace App\Tests\Extensions;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use PHPUnit\Runner\BeforeTestHook;

class CreateArtistHook implements BeforeTestHook
{

    public function executeBeforeTest(string $test): void
    {
        $this->artist = new Artist();
        $this->artist->email = 'firstuser@example.com';
        $this->artist->aboutMe = 'About me';
        $this->artist->profilePicture = 'profilepicture';
        $this->artist->artistName = 'First user';
        $this->artist->enabled = true;
        $this->artist->roles = [];
        $this->artist->setPassword('start1234');
        $this->artist->roles[] = 'ROLE_READER';
        $this->artist->roles[] = 'ROLE_WRITER';

        $this->artist->create();
        CurrentUser::$currentUser = $this->artist;
    }
}