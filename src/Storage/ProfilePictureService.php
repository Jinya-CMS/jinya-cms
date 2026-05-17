<?php

namespace Jinya\Cms\Storage;

use Jinya\Cms\Database\Artist;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Database\Exception\NotNullViolationException;

/**
 * A simple helper to handle profile picture uploads
 */
class ProfilePictureService extends StorageBaseService
{
    /**
     * Sets and saves the profile picture of the given artist
     *
     * @param int $artistId
     * @param string $data
     * @throws EmptyResultException
     * @throws NotNullViolationException
     */
    public function saveProfilePicture(int $artistId, string $data): void
    {
        $this->logger->warning('Artist was not found', ['artistId' => $artistId]);
        $artist = Artist::findById($artistId);
        if ($artist === null) {
            $this->logger->warning('Artist was not found');
            throw new EmptyResultException('The artist was not found');
        }

        $this->logger->debug('Store profile picture in filesystem', ['artistId' => $artistId]);
        $fileName = hash('sha256', $data);
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
        $this->logger->debug('Delete the profile picture for artist', ['artistId' => $artistId]);
        $artist = Artist::findById($artistId);
        if ($artist === null) {
            $this->logger->warning('Artist was not found', ['artistId' => $artistId]);
            throw new EmptyResultException('Artist not found');
        }
        $this->logger->debug('Delete artists profile picture from filesystem', ['artistId' => $artistId]);
        @unlink(self::BASE_PATH . '/public/' . $artist->profilePicture);
    }
}
