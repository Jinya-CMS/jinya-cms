<?php

namespace App\Web\Controllers;

use App\Database\Artist;
use App\Database\Exceptions\EmptyResultException;
use App\Storage\ProfilePictureService;
use App\Storage\StorageBaseService;
use App\Web\Middleware\AuthorizationMiddleware;
use Jinya\Database\Exception\NotNullViolationException;
use Jinya\Database\Exception\UniqueFailedException;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class ArtistController extends BaseController
{
    public function __construct(
        private readonly ProfilePictureService $profilePictureService = new ProfilePictureService()
    ) {
    }

    /**
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::POST, '/api/user')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
    public function createArtist(): ResponseInterface
    {
        $artist = new Artist();
        $artist->setPassword($this->body['password']);
        $artist->artistName = $this->body['artistName'];
        $artist->email = $this->body['email'];
        $artist->roles = $this->body['roles'];
        $artist->enabled = true;

        try {
            $artist->create();
        } catch (UniqueFailedException) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => 'Email already used',
                    'type' => 'unique-failed',
                ],
            ], self::HTTP_CONFLICT);
            /** @codeCoverageIgnore */
        } catch (NotNullViolationException) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => 'A required field is null',
                    'type' => 'not-null',
                ],
            ], self::HTTP_BAD_REQUEST);
        }

        return $this->json($artist->format(), self::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/user/{id}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
    public function updateArtist(int $id): ResponseInterface
    {
        $artist = Artist::findById($id);
        if ($artist === null) {
            return $this->entityNotFound('Artist not found');
        }

        if (array_key_exists('password', $this->body) && $this->body['password']) {
            $artist->setPassword($this->body['password']);
        }
        if (array_key_exists('artistName', $this->body) && $this->body['artistName']) {
            $artist->artistName = $this->body['artistName'];
        }
        if (array_key_exists('email', $this->body) && $this->body['email']) {
            $artist->email = $this->body['email'];
        }
        if (array_key_exists('roles', $this->body) && $this->body['roles']) {
            $artist->roles = $this->body['roles'];
        }

        try {
            $artist->update();
        } catch (UniqueFailedException) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => 'Email already used',
                    'type' => 'unique-failed',
                ],
            ], self::HTTP_CONFLICT);
            /** @codeCoverageIgnore */
        } catch (NotNullViolationException) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => 'A required field is null',
                    'type' => 'not-null',
                ],
            ], self::HTTP_BAD_REQUEST);
        }

        return $this->noContent();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::DELETE, '/api/user/{id}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
    public function deleteArtist(int $id): ResponseInterface
    {
        $artist = Artist::findById($id);
        if ($artist === null) {
            return $this->entityNotFound('Artist not found');
        }

        if (Artist::countAdmins($id) === 1) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => 'You cannot delete the last admin',
                    'type' => 'cannot-delete-last-admin',
                ],
            ], self::HTTP_CONFLICT);
        }

        $artist->delete();

        return $this->noContent();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     */
    #[Route(HttpMethod::PUT, '/api/user/{id}/activation')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
    public function activateArtist(int $id): ResponseInterface
    {
        $artist = Artist::findById($id);
        if ($artist === null) {
            return $this->entityNotFound('Artist not found');
        }

        $artist->enabled = true;
        $artist->update();

        return $this->noContent();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     */
    #[Route(HttpMethod::DELETE, '/api/user/{id}/activation')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
    public function deactivateArtist(int $id): ResponseInterface
    {
        $artist = Artist::findById($id);
        if ($artist === null) {
            return $this->entityNotFound('Artist not found');
        }

        if (Artist::countAdmins($id) === 1) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => 'You cannot deactivate the last admin',
                    'type' => 'cannot-deactivate-last-admin',
                ],
            ], self::HTTP_CONFLICT);
        }

        $artist->enabled = false;
        $artist->update();

        return $this->noContent();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/user/{id}/profilepicture')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getProfilePicture(int $id): ResponseInterface
    {
        $artist = Artist::findById($id);
        if ($artist === null) {
            return $this->entityNotFound('Artist not found');
        }

        return $this->file(StorageBaseService::PUBLIC_PATH . $artist->profilePicture);
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     */
    #[Route(HttpMethod::PUT, '/api/user/{id}/profilepicture')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
    public function uploadProfilePicture(int $id): ResponseInterface
    {
        try {
            $this->request->getBody()->rewind();
            $data = $this->request->getBody()->getContents();
            $this->profilePictureService->saveProfilePicture($id, $data);
        } catch (EmptyResultException) {
            return $this->entityNotFound('Artist not found');
        }

        return $this->noContent();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::DELETE, '/api/user/{id}/profilepicture')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
    public function deleteProfilePicture(int $id): ResponseInterface
    {
        try {
            $this->profilePictureService->deleteProfilePicture($id);
        } catch (EmptyResultException) {
            return $this->entityNotFound('Artist not found');
        }

        return $this->noContent();
    }
}
