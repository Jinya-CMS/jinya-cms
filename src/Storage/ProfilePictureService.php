<?php

namespace App\Storage;

use App\Database\Artist;
use App\Database\Exceptions\EmptyResultException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

/**
 *
 */
class ProfilePictureService extends StorageBaseService
{
    /**
     * Sets and saves the profile picture of the given artist
     *
     * @param int $artistId
     * @param string|resource|null $data
     * @throws EmptyResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function saveProfilePicture(int $artistId, mixed $data): void
    {
        if ($data === null) {
            throw new RuntimeException();
        }

        $artist = Artist::findById($artistId);
        if ($artist === null) {
            throw new EmptyResultException('The artist was not found');
        }

        if (is_string($data)) {
            $fileName = hash('sha256', $data);
        } elseif (is_resource($data)) {
            $fileName = $this->getFileHash($data);
        } else {
            $fileName = '';
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
        if ($artist === null) {
            throw new EmptyResultException('Artist not found');
        }
        unlink(self::BASE_PATH . $artist->profilePicture);
    }
}
