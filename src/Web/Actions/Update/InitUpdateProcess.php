<?php

namespace App\Web\Actions\Update;

use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to set the update cookie
 */
class InitUpdateProcess extends Action
{
    /**
     * Initializes the update process and generates a special key to allow updates
     *
     * @return Response
     */
    protected function action(): Response
    {
        $updateCode = hash('sha256', (string)time());
        $updateLock = __ROOT__ . '/update.lock';
        file_put_contents($updateLock, $updateCode);

        setcookie('JinyaUpdateKey', $updateCode, 0, '/');

        return $this->respond([], Action::HTTP_NO_CONTENT);
    }
}
