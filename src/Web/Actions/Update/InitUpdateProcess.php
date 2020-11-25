<?php

namespace App\Web\Actions\Update;

use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class InitUpdateProcess extends Action
{

    /**
     * @return Response
     * @throws JsonException
     */
    protected function action(): Response
    {
        $updateCode = sha1(time());
        $updateLock = __ROOT__ . '/update.lock';
        file_put_contents($updateLock, $updateCode);

        setcookie('JinyaUpdateKey', $updateCode, 0, '/');
        return $this->respond([], Action::HTTP_NO_CONTENT);
    }
}