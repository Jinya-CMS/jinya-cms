<?php

namespace App\Web\Actions\Artist\ProfilePicture;

use App\Database\Exceptions\EmptyResultException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Storage\ProfilePictureService;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/user/{id}/profilepicture', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::ADMIN)]
class DeleteProfilePictureAction extends Action
{

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
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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