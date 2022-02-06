<?php

namespace App\Storage;

use App\Database\Artist;
use App\Database\Exceptions\EmptyResultException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

class ProfilePictureService extends StorageBaseService
{
    /**
     * Sets and saves the profile picture of the given artist
     *
     * @param string|resource $data
     * @throws EmptyResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function saveProfilePicture(int $artistId, $data): void
    {
        $artist = Artist::findById($artistId);
        if (null === $artist) {
            throw new EmptyResultException('The artist was not found');
        }

        $fileName = '';
        if (is_string($data)) {
            $fileName = hash('sha256', $data);
        } elseif (is_resource($data)) {
            $fileName = $this->getFileHash($data);
        }
        file_put_contents(self::SAVE_PATH . $fileName, $data);
        $artist->profilePicture = self::WEB_PATH . $fileName;
        $artist->update();
    }

    /**
     * Deletes the artists profile picture
     *
     * @throws EmptyResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function deleteProfilePicture(int $artistId): void
    {
        $artist = Artist::findById($artistId);
        if (null === $artist) {
            throw new EmptyResultException('Artist not found');
        }
        unlink(self::BASE_PATH . $artist->profilePicture);
    }
}
