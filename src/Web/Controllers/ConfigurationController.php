<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Configuration\DatabaseConfigurationAdapter;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Stream;
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
        $databaseAdapter->set('api_key_expiry', $this->body['api_key_expiry'], 'jinya');
        $databaseAdapter->set('update_server', $this->body['update_server'], 'jinya');

        $databaseAdapter->set('from', $this->body['from'], 'mailer');
        $databaseAdapter->set('host', $this->body['host'], 'mailer');
        $databaseAdapter->set('port', $this->body['port'], 'mailer');
        $databaseAdapter->set('smtp_auth', $this->body['smtp_auth'], 'mailer');
        $databaseAdapter->set('username', $this->body['username'], 'mailer');
        $databaseAdapter->set('password', $this->body['password'], 'mailer');

        $data = parse_ini_file(__ROOT__.'/jinya-configuration.ini') ;
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
