<?php

namespace App\Web\Actions\Artist\ProfilePicture;

use App\Database\Exceptions\EmptyResultException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Storage\ProfilePictureService;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to upload a new profile picture
 */
class UploadProfilePictureAction extends Action
{
    private ProfilePictureService $profilePictureService;

    /**
     * UploadProfilePictureAction constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->profilePictureService = new ProfilePictureService();
    }

    /**
     * Takes the body and sets the body as new profile picture for the given artist
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        try {
            $this->profilePictureService->saveProfilePicture($id, $this->request->getBody()->detach());
        } catch (EmptyResultException) {
            throw new NoResultException($this->request, 'Artist not found');
        }

        return $this->noContent();
    }
}
