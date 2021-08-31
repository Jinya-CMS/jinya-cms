<?php

namespace App\Web\Actions\Version;

use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Update\UpdateAction;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/version', JinyaAction::GET)]
#[Authenticated(Authenticated::ADMIN)]
#[OpenApiRequest('This action gets the current version and the most recent version')]
#[OpenApiResponse('A successful response', example: [
    'currentVersion' => INSTALLED_VERSION,
    'mostRecentVersion' => INSTALLED_VERSION,
], exampleName: 'Returned versions', schema: [
    'currentVersion' => ['type' => 'string'],
    'mostRecentVersion' => ['type' => 'string'],
])]
class GetVersionInfo extends UpdateAction
{
    /**
     * {@inheritDoc}
     * @throws \JsonException
     */
    public function action(): Response
    {
        return $this->respond(
            [
                'currentVersion' => INSTALLED_VERSION,
                'mostRecentVersion' => array_key_last($this->getReleases()),
            ]
        );
    }
}
