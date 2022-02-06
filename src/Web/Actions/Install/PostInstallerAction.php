<?php

namespace App\Web\Actions\Install;

use App\Database\Artist;
use App\Database\Migrations\Migrator;
use App\Theming\ThemeSyncer;
use App\Web\Attributes\Authenticated;
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

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
        $postData = $this->request->getParsedBody();
        if (isset($postData['action'])) {
            $artist = new Artist();
            $artist->email = $postData['email'];
            $artist->setPassword($postData['password']);
            $artist->artistName = $postData['artistname'];
            $artist->roles = [
                Authenticated::READER,
                Authenticated::WRITER,
                Authenticated::ADMIN,
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
                        'data' => $postData,
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

        $data = array_map(static fn(string $key, string $value) => "$key=$value", array_keys($postData), $postData);

        $dotenv .= PHP_EOL . implode(PHP_EOL, $data);

        file_put_contents(__ROOT__ . '/.env', $dotenv);

        $dotenvUtil = Dotenv::createUnsafeImmutable(__ROOT__);
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
