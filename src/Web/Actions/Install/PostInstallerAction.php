<?php

namespace App\Web\Actions\Install;

use App\Authentication\AuthenticationChecker;
use App\Database\Artist;
use App\Database\Migrations\Migrator;
use App\Theming\ThemeSyncer;
use App\Web\Middleware\RoleMiddleware;
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 *
 */
class PostInstallerAction extends InstallAction
{
    /**
     * {@inheritDoc}
     * @throws Throwable
     * @throws Throwable
     * @throws Throwable
     */
    protected function action(): Response
    {
        if (isset($this->body['action'])) {
            $artist = new Artist();
            $artist->email = $this->body['email'];
            $artist->setPassword($this->body['password']);
            $artist->artistName = $this->body['artistname'];
            $artist->roles = [
                AuthenticationChecker::ROLE_READER,
                AuthenticationChecker::ROLE_WRITER,
                AuthenticationChecker::ROLE_ADMIN,
            ];
            $artist->enabled = true;
            try {
                $themeSyncer = new ThemeSyncer();
                $themeSyncer->syncThemes();
                $artist->create();

                touch(__ROOT__ . '/installed.lock');

                return $this->response
                    ->withStatus(self::HTTP_MOVED_PERMANENTLY)
                    ->withHeader('Location', '/designer');
            } catch (Throwable $exception) {
                return $this->render(
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

        $data = array_map(static fn(string $key, string $value) => "$key=$value", array_keys($this->body), $this->body);

        $dotenv .= PHP_EOL . implode(PHP_EOL, $data);

        file_put_contents(__ROOT__ . '/.env', $dotenv);

        $dotenvUtil = Dotenv::createUnsafeImmutable(__ROOT__);
        $dotenvUtil->load();

        try {
            Migrator::migrate();
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());

            return $this->render('install::set-config', ['data' => $this->body, 'error' => $exception->getMessage()]);
        }

        return $this->render('install::first-admin', []);
    }
}
