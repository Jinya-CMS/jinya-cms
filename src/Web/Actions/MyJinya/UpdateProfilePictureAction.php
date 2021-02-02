<?php

namespace App\Web\Actions\MyJinya;

use App\Database\Artist;
use App\Database\Exceptions\EmptyResultException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Storage\ProfilePictureService;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/me/profilepicture', JinyaAction::PUT)]
#[Authenticated]
#[OpenApiRequest('This action uploads a new profile picture')]
#[OpenApiResponse('Successfully uploaded the profile picture', statusCode: Action::HTTP_NO_CONTENT)]
class UpdateProfilePictureAction extends Action
{
    /**
     * @throws EmptyResultException
     * @throws JsonException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);
        $profileStorage = new ProfilePictureService();
        $profileStorage->saveProfilePicture($currentArtist->id, $this->request->getBody()->detach());

        return $this->noContent();
    }
}
