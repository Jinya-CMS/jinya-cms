<?php

namespace App\Web\Controllers;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Database\Exceptions\EmptyResultException;
use App\Storage\ProfilePictureService;
use App\Web\Middleware\AuthorizationMiddleware;
use Jinya\Database\Exception\NotNullViolationException;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares(new AuthorizationMiddleware())]
class MyJinyaController extends BaseController
{
    public function __construct(
        private readonly ProfilePictureService $profilePictureService = new ProfilePictureService()
    ) {
    }

    /**
     * Gets the information of the current artist
     *
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/me')]
    public function getMyProfile(): ResponseInterface
    {
        return $this->json(CurrentUser::$currentUser->format());
    }

    /**
     * Updates about me data of the artist, currently contains email, artist name and about me info
     *
     * @return ResponseInterface
     * @throws NotNullViolationException
     */
    #[Route(HttpMethod::PUT, '/api/me')]
    public function updateMyProfile(): ResponseInterface
    {
        /** @var Artist $currentArtist */
        $currentArtist = CurrentUser::$currentUser;

        if (isset($this->body['email'])) {
            $currentArtist->email = $this->body['email'];
        }

        if (isset($this->body['artistName'])) {
            $currentArtist->artistName = $this->body['artistName'];
        }

        if (isset($this->body['aboutMe'])) {
            $currentArtist->aboutMe = $this->body['aboutMe'];
        }

        $currentArtist->update();

        return $this->noContent();
    }

    /**
     * Updates the color scheme to the new value
     *
     * @return ResponseInterface
     * @throws NotNullViolationException
     */
    #[Route(HttpMethod::PUT, '/api/me/colorscheme')]
    public function updateColorScheme(): ResponseInterface
    {
        $colorScheme = $this->body['colorScheme'];
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST);

        if ($colorScheme === 'light') {
            $currentArtist->prefersColorScheme = false;
        } elseif ($colorScheme === 'dark') {
            $currentArtist->prefersColorScheme = true;
        } else {
            $currentArtist->prefersColorScheme = null;
        }

        $currentArtist->update();

        return $this->noContent();
    }

    /**
     * @throws NotNullViolationException
     * @throws EmptyResultException
     */
    #[Route(HttpMethod::PUT, '/api/me/profilepicture')]
    #[Middlewares(new AuthorizationMiddleware())]
    public function uploadProfilePicture(): ResponseInterface
    {
        $this->profilePictureService->saveProfilePicture(
            CurrentUser::$currentUser->id,
            $this->request->getBody()->detach()
        );

        return $this->noContent();
    }
}
