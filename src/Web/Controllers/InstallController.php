<?php

namespace Jinya\Cms\Web\Controllers;

use Dotenv\Dotenv;
use Jinya\Cms\Database\Artist;
use Jinya\Cms\Database\Migrations\Migrator;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Engine;
use Jinya\Cms\Theming\ThemeSyncer;
use Jinya\Cms\Web\Middleware\RedirectInstallerMiddleware;
use Jinya\Database\Entity;
use Jinya\Plates\Engine as PlatesEngine;
use Jinya\Plates\Extension\URI;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @codeCoverageIgnore
 */
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
        return $this->renderThemed('install::installer', ['data' => []]);
    }

    #[Route(HttpMethod::POST, 'api/install/configuration')]
    public function createConfiguration(): ResponseInterface
    {
        $dotenv = <<<'DOTENV'
APP_ENV=prod

JINYA_API_KEY_EXPIRY=86400
JINYA_UPDATE_SERVER=https://releases.jinya.de/cms

DOTENV;

        $dotenv .= 'MYSQL_HOST=' . $this->body['mysql']['host'] . "\n";
        $dotenv .= 'MYSQL_PORT=' . $this->body['mysql']['port'] . "\n";
        $dotenv .= 'MYSQL_DATABASE=' . $this->body['mysql']['database'] . "\n";
        $dotenv .= 'MYSQL_USER=' . $this->body['mysql']['username'] . "\n";
        $dotenv .= 'MYSQL_PASSWORD=' . $this->body['mysql']['password'] . "\n";
        $dotenv .= "\n";
        $dotenv .= 'MAILER_HOST=' . $this->body['mailing']['host'] . "\n";
        $dotenv .= 'MAILER_PORT=' . $this->body['mailing']['port'] . "\n";
        $dotenv .= 'MAILER_USERNAME=' . $this->body['mailing']['username'] . "\n";
        $dotenv .= 'MAILER_PASSWORD=' . $this->body['mailing']['password'] . "\n";
        $dotenv .= 'MAILER_ENCRYPTION=' . $this->body['mailing']['encryption'] . "\n";
        $dotenv .= 'MAILER_FROM=' . $this->body['mailing']['from'] . "\n";
        $dotenv .= 'MAILER_SMTP_AUTH=' . $this->body['mailing']['authRequired'] . "\n";

        try {
            file_put_contents(__ROOT__ . '/.env', $dotenv);

            $dotenvUtil = Dotenv::createUnsafeImmutable(__ROOT__);
            $dotenvUtil->load();
        } catch (Throwable) {
            return new Response(500, body: Stream::create('Failed to save the environment variables'));
        }

        try {
            Entity::getPDO()->exec('SELECT 1');
        } catch (Throwable) {
            @unlink(__ROOT__ . '/.env');
            return new Response(500, body: Stream::create('We were not possible to connect to the database'));
        }

        return $this->noContent();
    }

    #[Route(HttpMethod::POST, 'api/install/database')]
    public function createDatabase(): ResponseInterface
    {
        try {
            Migrator::migrate();
            $themeSyncer = new ThemeSyncer();
            $themeSyncer->syncThemes();
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());

            return new Response(500, body: Stream::create($exception->getMessage()));
        }

        return $this->noContent();
    }

    #[Route(HttpMethod::POST, 'api/install/admin')]
    public function createAdmin(): ResponseInterface
    {
        $artist = new Artist();
        $artist->email = $this->body['email'];
        $artist->setPassword($this->body['password']);
        $artist->artistName = $this->body['artistName'];
        $artist->roles = [
            ROLE_READER,
            ROLE_WRITER,
            ROLE_ADMIN,
        ];
        $artist->enabled = true;
        try {
            $artist->create();

            touch(__ROOT__ . '/installed.lock');

            return $this->noContent();
        } catch (Throwable $exception) {
            return new Response(500, body: Stream::create($exception->getMessage()));
        }
    }
}
