<?php

namespace App\Web\Actions\Install;

use Psr\Http\Message\ResponseInterface as Response;

class GetInstallerAction extends InstallAction
{
    public function action(): Response
    {
        return $this->render('install::set-config', ['data' => [

        ]]);
    }
}
