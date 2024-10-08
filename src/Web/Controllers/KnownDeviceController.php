<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\KnownDevice;
use Jinya\Cms\Utils\CookieSetter;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class KnownDeviceController extends BaseController
{
    /**
     * Returns a list of all known devices from the current user
     *
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, 'api/known-device')]
    #[Middlewares(new AuthorizationMiddleware())]
    public function getKnownDevices(): ResponseInterface
    {
        return $this->jsonIterator(KnownDevice::findByArtist(CurrentUser::$currentUser->id));
    }

    /**
     * Deletes the known device by the given key
     *
     * @param string $key
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::DELETE, 'api/known-device/{key}')]
    #[Middlewares(new AuthorizationMiddleware())]
    public function deleteKnownDevice(string $key): ResponseInterface
    {
        $device = KnownDevice::findByCode($key);
        if ($device === null) {
            return $this->entityNotFound('Known device not found');
        }

        $device->delete();

        $cookie = $this->request->getCookieParams()[self::DEVICE_CODE_COOKIE];
        if ($cookie === $key) {
            return CookieSetter::unsetCookie($this->noContent(), self::DEVICE_CODE_COOKIE, false);
        }

        return $this->noContent();
    }

    /**
     * Checks if the known device is valid and known
     *
     * @param string $key
     * @return ResponseInterface
     */
    #[Route(HttpMethod::HEAD, 'api/known-device/{key}')]
    public function validateKnownDevice(string $key): ResponseInterface
    {
        if (KnownDevice::findByCode($key)) {
            return $this->noContent();
        }

        return new Response(self::HTTP_FORBIDDEN);
    }
}
