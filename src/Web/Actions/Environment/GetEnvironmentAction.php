<?php

namespace App\Web\Actions\Environment;

use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get the env variables
 */
class GetEnvironmentAction extends Action
{
    /**
     * Gets all environment variables
     *
     * @return Response
     */
    protected function action(): Response
    {
        $env = array_filter(
            $_ENV,
            static fn ($key) => str_starts_with($key, 'MAILER') || str_starts_with($key, 'JINYA') || str_starts_with(
                $key,
                'MYSQL'
            ) || str_starts_with($key, 'MATOMO'),
            ARRAY_FILTER_USE_KEY
        );
        $data = array_map(
            static fn ($key, $value) => [
                'key' => $key,
                'value' => stripos($key, 'password') === false ? $value : '••••••',
            ],
            array_keys($env),
            array_values($env)
        );

        return $this->respond($data);
    }
}
