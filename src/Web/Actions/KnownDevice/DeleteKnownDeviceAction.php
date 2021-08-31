<?php

namespace App\Web\Actions\KnownDevice;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\KnownDevice;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/account/known_device/{key}', JinyaAction::DELETE)]
#[JinyaAction('/api/known_device/{key}', JinyaAction::DELETE, name: 'delete_known_device_key')]
#[Authenticated]
#[OpenApiRequest('This action deletes the given known device')]
#[OpenApiParameter('key', true)]
#[OpenApiResponse('Known device was deleted', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Known device unknown', example: OpenApiResponse::NOT_FOUND, exampleName: 'Known device unknown', statusCode: Action::HTTP_FORBIDDEN, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteKnownDeviceAction extends Action
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
        $device = KnownDevice::findByCode($this->args['key']);
        if (null === $device) {
            throw new NoResultException($this->request, 'Known device not found');
        }

        $device->delete();

        return $this->noContent();
    }
}
