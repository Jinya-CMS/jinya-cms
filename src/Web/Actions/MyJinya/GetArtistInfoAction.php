<?php

namespace App\Web\Actions\MyJinya;

use App\Database\Menu;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Middleware\AuthenticationMiddleware;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/account', JinyaAction::GET)]
#[JinyaAction('/api/me', JinyaAction::GET, name: 'get_me')]
#[Authenticated]
#[OpenApiRequest('This action gets the artist info')]
#[OpenApiResponse('Successfully got the artist info', example: [
    'artistName' => OpenApiResponse::FAKER_NAME,
    'email' => OpenApiResponse::FAKER_EMAIL,
    'profilePicture' => OpenApiResponse::FAKER_SHA1,
    'roles' => ['ROLE_WRITER'],
    'enabled' => true,
    'id' => 1,
    'aboutMe' => OpenApiResponse::FAKER_PARAGRAPH,
], exampleName: 'Returned artist info', ref: Menu::class)]
class GetArtistInfoAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     */
    public function action(): Response
    {
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        return $this->respond($currentArtist->format());
    }
}
