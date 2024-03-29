<?php

namespace App\Web\Actions\Artist\ProfilePicture;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Storage\StorageBaseService;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get the profile picture
 */
class GetProfilePictureAction extends Action
{
    /**
     * Returns the profile picture as file
     *
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $artist = Artist::findById($id);
        if ($artist === null) {
            throw new NoResultException($this->request, 'Artist not found');
        }

        $path = $artist->profilePicture;

        return $this->respondFile($path, mime_content_type(StorageBaseService::BASE_PATH . '/public/' . $path) ?: 'application/octet-stream');
    }
}
