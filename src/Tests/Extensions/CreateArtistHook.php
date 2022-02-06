<?php

namespace App\Tests\Extensions;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use PHPUnit\Runner\BeforeTestHook;

class CreateArtistHook implements BeforeTestHook
{

    /**
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     */
    public function executeBeforeTest(string $test): void
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
}