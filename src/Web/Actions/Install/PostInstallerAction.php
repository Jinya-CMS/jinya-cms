<?php

namespace App\Web\Actions\Install;

use App\Database\Artist;
use App\Database\Migrations\Migrator;
use App\Database\Theme;
use App\Theming\ThemeSyncer;
use App\Web\Middleware\RoleMiddleware;
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

class PostInstallerAction extends InstallAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $postData = $this->request->getParsedBody();
        if (isset($postData['action'])) {
            $artist = new Artist();
            $artist->email = $postData['email'];
            $artist->setPassword($postData['password']);
            $artist->artistName = $postData['artistname'];
            $artist->roles = [
                RoleMiddleware::ROLE_WRITER,
                RoleMiddleware::ROLE_ADMIN,
                RoleMiddleware::ROLE_SUPER_ADMIN,
            ];
            $artist->enabled = true;
            try {
                $themeSyncer = new ThemeSyncer();
                $themeSyncer->syncThemes();
                $artist->create();

                return $this->response->withStatus(self::HTTP_MOVED_PERMANENTLY)->withHeader('Location',
                    '/');
            } catch (Throwable $exception) {
                return $this->render('install::first-admin', [
                    'data' => $postData,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $dotenv = <<<'DOTENV'
APP_ENV=prod

JINYA_API_KEY_EXPIRY=86400

DOTENV;

        $data = array_map(fn(string $key, string $value) => "$key=$value", array_keys($postData), $postData);

        foreach ($data as $datum) {
            $dotenv .= PHP_EOL . $datum;
        }

        file_put_contents(__ROOT__ . '/.env', $dotenv);

        $dotenvUtil = Dotenv::createImmutable(__ROOT__);
        $dotenvUtil->load();

        try {
            Migrator::migrate();
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());

            return $this->render('install::set-config', ['data' => $postData, 'error' => $exception->getMessage()]);
        }

        return $this->render('install::first-admin', []);
    }
}