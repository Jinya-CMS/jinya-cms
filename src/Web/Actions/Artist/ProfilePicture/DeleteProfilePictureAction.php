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
 * Action to delete the profile picture
 */
class DeleteProfilePictureAction extends Action
{
    /** @var ProfilePictureService The profile picture service */
    private ProfilePictureService $profilePictureService;

    /**
     * DeleteProfilePictureAction constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->profilePictureService = new ProfilePictureService();
    }

    /**
     * Action that deletes the profile picture for the given user
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
        try {
            $this->profilePictureService->deleteProfilePicture($this->args['id']);
        } catch (EmptyResultException) {
            throw new NoResultException($this->request, 'Artist not found');
        }

        return $this->noContent();
    }
}
