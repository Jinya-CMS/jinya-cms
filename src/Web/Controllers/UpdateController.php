<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\Migrations\Migrator;
use Jinya\Cms\Utils\CacheUtils;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use ZipArchive;

/** @codeCoverageIgnore */
#[Controller]
#[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
class UpdateController extends BaseController
{
    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, 'api/version')]
    public function getVersionInfo(): ResponseInterface
    {
        return $this->json([
            'currentVersion' => INSTALLED_VERSION,
            'mostRecentVersion' => array_key_last($this->getReleases()),
        ]);
    }

    /**
     * Gets all currently available releases from the configured update server
     *
     * @return array<string, string>
     * @throws JsonException
     */
    protected function getReleases(): array
    {
        return json_decode(
            file_get_contents(getenv('JINYA_UPDATE_SERVER') ?: '') ?: '',
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @throws JsonException
     * @throws ReflectionException
     */
    #[Route(HttpMethod::PUT, 'api/update')]
    public function updateJinya(): ResponseInterface
    {
        $updatePath = __ROOT__ . '/var/update.zip';
        $newVersion = array_key_last($this->getReleases());
        $releasePath = $this->getReleasePath($newVersion);
        copy($releasePath, __ROOT__ . '/var/update.zip');
        $zipStream = new ZipArchive();
        $zipStream->open($updatePath);
        $zipStream->extractTo(__ROOT__);
        $zipStream->close();
        Migrator::migrate();

        CacheUtils::clearRouterCache();
        CacheUtils::clearDatabaseCache();
        CacheUtils::clearOpcache();

        CacheUtils::recreateRoutingCache();

        return $this->noContent();
    }

    /**
     * Gets the path of the given release version
     *
     * @param string $version
     * @return string
     * @throws JsonException
     */
    protected function getReleasePath(string $version): string
    {
        $releases = $this->getReleases();
        return $releases[$version];
    }
}
