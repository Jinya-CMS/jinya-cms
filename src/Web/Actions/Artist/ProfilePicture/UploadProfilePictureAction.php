<?php

namespace App\Web\Actions\Artist\ProfilePicture;

use App\Database\Exceptions\EmptyResultException;
use App\Database\Exceptions\UniqueFailedException;
use App\Storage\ProfilePictureService;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class UploadProfilePictureAction extends Action
{
    private ProfilePictureService $profilePictureService;

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        try {
            $this->profilePictureService->saveProfilePicture($id, $this->request->getBody()->detach());
        }catch (EmptyResultException $exception) {
            throw new NoResultException($this->request,'Artist not found');
        }

        return $this->noContent();
    }
}