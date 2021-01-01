<?php

namespace App\Web\Actions\Artist\ProfilePicture;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user/{id}/profilepicture', JinyaAction::GET)]
#[Authenticated(role: Authenticated::ADMIN)]
class GetProfilePictureAction extends Action
{
    /**
     * @inheritDoc
     * @return Response
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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