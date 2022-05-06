<?php

namespace App\Web\Actions\Environment;

use App\Web\Actions\Action;
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class GetEnvironmentAction extends Action
{
    protected function action(): Response
    {
        $env = Dotenv::parse(file_get_contents(__ROOT__ . '/.env'));
        $data = array_map(
            static fn($key, $value) => [
                'key' => $key,
                'value' => stripos($key, 'password') === false ? $value : '••••••',
            ],
            array_keys($env),
            array_values($env)
        );

        return $this->respond($data);
    }
}
