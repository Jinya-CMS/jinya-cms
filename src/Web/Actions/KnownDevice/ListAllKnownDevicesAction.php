<?php

namespace App\Web\Actions\KnownDevice;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\KnownDevice;
use App\OpenApiGeneration\Attributes\OpenApiListResponse;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/account/known_device', JinyaAction::GET)]
#[JinyaAction('/api/known_device', JinyaAction::GET, name: 'list_all_known_device_key')]
#[Authenticated]
#[OpenApiRequest('This action lists all known devices')]
#[OpenApiListResponse('Successfully got the known devices', example: [
    'remoteAddress' => OpenApiResponse::FAKER_IPV4,
    'userAgent' => OpenApiResponse::FAKER_USER_AGENT,
    'key' => OpenApiResponse::FAKER_SHA1,
], exampleName: 'List of known devices', ref: KnownDevice::class)]
class ListAllKnownDevicesAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        return $this->respondList($this->formatIterator(KnownDevice::findByArtist($currentArtist->id)));
    }
}
