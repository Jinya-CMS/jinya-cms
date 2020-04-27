<?php

namespace App\Web\Actions\Artist\ProfilePicture;

use App\Database\Artist;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

class GetProfilePictureAction extends Action
{
    /**
     * @inheritDoc
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $artist = Artist::findById($id);
        if ($artist === null) {
            throw new NoResultException($this->request, 'Artist not found');
        }

        $path = $artist->profilePicture;

        return $this->respondFile($path, mime_content_type($path));
    }
}