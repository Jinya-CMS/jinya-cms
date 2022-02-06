<?php

namespace App\Web\Actions\Install;

use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

class GetInstallerAction extends InstallAction
{
    /**
     * @throws Throwable
     */
    protected function action(): Response
    {
        return $this->render('install::set-config', ['data' => [

        ]]);
    }
}
