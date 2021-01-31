<?php

namespace App\Web\Actions\Artist\ProfilePicture;

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
use App\Web\Exceptions\NoResultException;
use JetBrains\PhpStorm\Pure;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

#[JinyaAction('/api/user/{id}/profilepicture', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::ADMIN)]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequest('This action deletes the given profile picture')]
#[OpenApiResponse('Successfully deleted the profile picture', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Not authenticated', example: OpenApiResponse::INVALID_API_KEY, exampleName: 'Invalid API key', statusCode: Action::HTTP_FORBIDDEN, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
#[OpenApiResponse('Artist not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Artist not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteProfilePictureAction extends Action
{

    private ProfilePictureService $profilePictureService;

    /**
     * DeleteProfilePictureAction constructor.
     * @param LoggerInterface $logger
     * @param ProfilePictureService $profilePictureService
     */
    #[Pure] public function __construct(LoggerInterface $logger, ProfilePictureService $profilePictureService)
    {
        parent::__construct($logger);
        $this->profilePictureService = $profilePictureService;
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