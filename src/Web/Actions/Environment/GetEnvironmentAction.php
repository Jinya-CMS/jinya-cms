<?php

namespace App\Web\Actions\Environment;

use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetEnvironmentAction extends Action
{

    /**
     * @return Response
     * @throws JsonException
     */
    protected function action(): Response
    {
        $data = array_map(fn($key, $value) => [
            'key' => $key,
            'value' => stripos($key, 'password') === false ? $value : '******',
        ], array_keys($_ENV), array_values($_ENV));

        return $this->respond($data);
    }
}