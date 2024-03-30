<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\Artist;
use Jinya\Cms\Database\Migrations\Migrator;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Engine;
use Jinya\Cms\Theming\ThemeSyncer;
use Jinya\Cms\Web\Middleware\RedirectInstallerMiddleware;
use Dotenv\Dotenv;
use Jinya\Plates\Engine as PlatesEngine;
use Jinya\Plates\Extension\URI;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

#[Controller]
#[Middlewares(new RedirectInstallerMiddleware())]
class InstallController extends BaseController
{
    /** @var PlatesEngine The Plates engine */
    private PlatesEngine $engine;

    private readonly LoggerInterface $logger;

    public function __construct()
    {
        $this->engine = Engine::getPlatesEngine();
        $this->engine->addFolder('install', __DIR__ . '/../Templates/Installer');

        $this->logger = Logger::getLogger();
    }

    /**
     * Renders the given template with the given data
     *
     * @param string $template
     * @param array<mixed> $data
     * @return ResponseInterface
     * @throws Throwable
     */
    private function renderThemed(string $template, array $data): ResponseInterface
    {
        $this->engine->loadExtension(new URI($this->request->getUri()->getPath()));
        $renderResult = $this->engine->render($template, $data);

        return new Response(self::HTTP_OK, ['Content-Type' => 'text/html'], $renderResult);
    }

    /**
     * Renders the installer page
     * @throws Throwable
     */
    #[Route(HttpMethod::GET, 'install')]
    public function getInstall(): ResponseInterface
    {
        return $this->renderThemed('install::set-config', ['data' => []]);
    }

    /**
     * Executes the post for the installer. This creates the first admin and .env file
     * @throws Throwable
     * @throws Throwable
     * @throws Throwable
     */
    #[Route(HttpMethod::POST, 'installer')]
    public function postInstall(): ResponseInterface
    {
        $body = $_POST;
        if (isset($body['action'])) {
            $artist = new Artist();
            $artist->email = $body['email'];
            $artist->setPassword($body['password']);
            $artist->artistName = $body['artistname'];
            $artist->roles = [
                ROLE_READER,
                ROLE_WRITER,
                ROLE_ADMIN,
            ];
            $artist->enabled = true;
            try {
                $themeSyncer = new ThemeSyncer();
                $themeSyncer->syncThemes();
                $artist->create();

                touch(__ROOT__ . '/installed.lock');

                return new Response(self::HTTP_MOVED_PERMANENTLY, ['Location' => '/designer']);
            } catch (Throwable $exception) {
                return $this->renderThemed(
                    'install::first-admin',
                    [
                        'data' => $this->body,
                        'error' => $exception->getMessage(),
                    ]
                );
            }
        }

        $dotenv = <<<'DOTENV'
APP_ENV=prod

JINYA_API_KEY_EXPIRY=86400
JINYA_UPDATE_SERVER=https://releases.jinya.de/cms

DOTENV;

        $data = array_map(static fn (string $key, string $value) => "$key=$value", array_keys($body), $body);

        $dotenv .= PHP_EOL . implode(PHP_EOL, $data);

        file_put_contents(__ROOT__ . '/.env', $dotenv);

        $dotenvUtil = Dotenv::createUnsafeImmutable(__ROOT__);
        $dotenvUtil->load();

        try {
            Migrator::migrate();
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());

            return $this->renderThemed(
                'install::set-config',
                ['data' => $body, 'error' => $exception->getMessage()]
            );
        }

        return $this->renderThemed('install::first-admin', []);
    }
}
