<?php

namespace App\Web\Actions\Environment;

use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Dotenv\Dotenv;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/environment', JinyaAction::GET)]
#[Authenticated(role: Authenticated::ADMIN)]
class GetEnvironmentAction extends Action
{

    /**
     * @return Response
     * @throws JsonException
     */
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