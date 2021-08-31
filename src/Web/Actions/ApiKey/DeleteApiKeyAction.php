<?php

namespace App\Web\Actions\ApiKey;

use App\Database\ApiKey;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/api_key/{key}', JinyaAction::DELETE)]
#[Authenticated]
#[OpenApiRequest('This action deletes the given api key')]
#[OpenApiParameter('key', true)]
#[OpenApiResponse('Successfully delete the api key', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('API key not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'API key not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteApiKeyAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $device = ApiKey::findByApiKey($this->args['key']);
        if (null === $device) {
            throw new NoResultException($this->request, 'Api key not found');
        }

        $device->delete();

        return $this->noContent();
    }
}
