<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Configuration\DatabaseConfigurationAdapter;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use Psr\Http\Message\ResponseInterface;
use Throwable;

#[Controller('api/configuration')]
class ConfigurationController extends BaseController
{
    #[Route(HttpMethod::GET)]
    #[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
    public function getConfiguration(): ResponseInterface
    {
        $mysql = JinyaConfiguration::getConfiguration()->getAll('mysql');
        $mailer = JinyaConfiguration::getConfiguration()->getAll('mailer');
        $jinya = JinyaConfiguration::getConfiguration()->getAll('jinya');

        $mysql['password'] = '';
        $mailer['password'] = '';

        return $this->json([
            'mysql' => $mysql,
            'mailer' => $mailer,
            'jinya' => $jinya,
        ]);
    }

    #[Route(HttpMethod::PUT)]
    #[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
    public function updateConfiguration(): ResponseInterface
    {
        $databaseAdapter = new DatabaseConfigurationAdapter();
        if (array_key_exists('jinya', $this->body)) {
            $jinya = $this->body['jinya'];
            if (array_key_exists('api_key_expiry', $jinya)) {
                $databaseAdapter->set('api_key_expiry', $jinya['api_key_expiry'], 'jinya');
            }
            if (array_key_exists('update_server', $jinya)) {
                $databaseAdapter->set('update_server', $jinya['update_server'], 'jinya');
            }
            if (array_key_exists('ip_database_url', $jinya)) {
                $databaseAdapter->set('ip_database_url', $jinya['ip_database_url'], 'jinya');
            }
        }

        if (array_key_exists('mailer', $this->body)) {
            $mailer = $this->body['mailer'];
            if (array_key_exists('from', $mailer)) {
                $databaseAdapter->set('from', $mailer['from'], 'mailer');
            }
            if (array_key_exists('host', $mailer)) {
                $databaseAdapter->set('host', $mailer['host'], 'mailer');
            }
            if (array_key_exists('port', $mailer)) {
                $databaseAdapter->set('port', $mailer['port'], 'mailer');
            }
            if (array_key_exists('smtp_auth', $mailer)) {
                $databaseAdapter->set('smtp_auth', $mailer['smtp_auth'], 'mailer');
            }
            if (array_key_exists('username', $mailer)) {
                $databaseAdapter->set('username', $mailer['username'], 'mailer');
            }
            if (array_key_exists('password', $mailer)) {
                $databaseAdapter->set('password', $mailer['password'], 'mailer');
            }
            if (array_key_exists('encryption', $mailer)) {
                $databaseAdapter->set('encryption', $mailer['encryption'], 'mailer');
            }
        }

        if (array_key_exists('mysql', $this->body)) {
            $data = parse_ini_file(__ROOT__.'/jinya-configuration.ini', true, INI_SCANNER_TYPED);
            if (!$data) {
                $data = [];
            }

            $mysql = $this->body['mysql'];
            try {
                $data['mysql'] = $data['mysql'] ?? [];
                $data['mysql']['database'] = $mysql['database'] ?? $data['mysql']['database'];
                $data['mysql']['host'] = $mysql['host'] ?? $data['mysql']['host'];
                $data['mysql']['password'] = $mysql['password'] ?? $data['mysql']['password'];
                $data['mysql']['port'] = $mysql['port'] ?? $data['mysql']['port'];
                $data['mysql']['user'] = $mysql['user'] ?? $data['mysql']['user'];
                $ini = '';
                foreach ($data as $group => $items) {
                    $ini .= "[$group]\n";
                    foreach ($items as $key => $value) {
                        $ini .= "$key=$value\n";
                    }
                    $ini .= "\n";
                }

                file_put_contents(__ROOT__ . '/jinya-configuration.ini', $ini);
            } catch (Throwable) {
                return $this->json([
                    'success' => false,
                    'error' => [
                        'message' => 'The ini file could not be saved',
                        'type' => 'failed-to-save',
                    ],
                ], self::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $this->noContent();
    }
}
