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
        if (array_key_exists('jinya_api_key_expiry', $this->body)) {
            $databaseAdapter->set('api_key_expiry', $this->body['jinya_api_key_expiry'], 'jinya');
        }
        if (array_key_exists('jinya_update_server', $this->body)) {
            $databaseAdapter->set('update_server', $this->body['jinya_update_server'], 'jinya');
        }

        if (array_key_exists('mailer_from', $this->body)) {
            $databaseAdapter->set('from', $this->body['mailer_from'], 'mailer');
        }
        if (array_key_exists('mailer_host', $this->body)) {
            $databaseAdapter->set('host', $this->body['mailer_host'], 'mailer');
        }
        if (array_key_exists('mailer_port', $this->body)) {
            $databaseAdapter->set('port', $this->body['mailer_port'], 'mailer');
        }
        if (array_key_exists('mailer_smtp_auth', $this->body)) {
            $databaseAdapter->set('smtp_auth', $this->body['mailer_smtp_auth'], 'mailer');
        }
        if (array_key_exists('mailer_username', $this->body)) {
            $databaseAdapter->set('username', $this->body['mailer_username'], 'mailer');
        }
        if (array_key_exists('mailer_password', $this->body)) {
            $databaseAdapter->set('password', $this->body['mailer_password'], 'mailer');
        }
        if (array_key_exists('mailer_encryption', $this->body)) {
            $databaseAdapter->set('encryption', $this->body['mailer_encryption'], 'mailer');
        }

        $data = parse_ini_file(__ROOT__.'/jinya-configuration.ini', true, INI_SCANNER_TYPED);
        if (!$data) {
            $data = [];
        }
        try {
            $data['mysql'] = $data['mysql'] ?? [];
            $data['mysql']['database'] = $this->body['mysql']['database'] ?? $data['mysql']['database'];
            $data['mysql']['host'] = $this->body['mysql']['host'] ?? $data['mysql']['host'];
            $data['mysql']['password'] = $this->body['mysql']['password'] ?? $data['mysql']['password'];
            $data['mysql']['port'] = $this->body['mysql']['port'] ?? $data['mysql']['port'];
            $data['mysql']['user'] = $this->body['mysql']['user'] ?? $data['mysql']['user'];
            $ini = '';
            foreach ($data as $group => $items) {
                $ini .= "[$group]\n";
                foreach ($items as $key => $value) {
                    $ini .= "$key=$value\n";
                }
                $ini .= "\n";
            }

            file_put_contents(__ROOT__.'/jinya-configuration.ini', $ini);
        } catch (Throwable) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => 'The ini file could not be saved',
                    'type' => 'failed-to-save',
                ],
            ], self::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->noContent();
    }
}
