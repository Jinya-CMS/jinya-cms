<?php

namespace App\Storage;

use App\Database\Artist;
use App\Database\Exceptions\EmptyResultException;
use Jinya\Database\Exception\NotNullViolationException;
use RuntimeException;

/**
 * A simple helper to handle profile picture uploads
 */
class ProfilePictureService extends StorageBaseService
{
    /**
     * Sets and saves the profile picture of the given artist
     *
     * @param int $artistId
     * @param string|resource|null $data
     * @throws EmptyResultException
     * @throws NotNullViolationException
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
            throw new RuntimeException();
        }
        file_put_contents(self::SAVE_PATH . $fileName, $data);
        $artist->profilePicture = self::WEB_PATH . $fileName;
        $artist->update();
    }

    /**
     * Deletes the artists' profile picture
     *
     * @param int $artistId
     * @throws EmptyResultException
     */
    public function deleteProfilePicture(int $artistId): void
    {
        $artist = Artist::findById($artistId);
        if ($artist === null) {
            throw new EmptyResultException('Artist not found');
        }
        unlink(self::BASE_PATH . '/public/' . $artist->profilePicture);
    }
}
