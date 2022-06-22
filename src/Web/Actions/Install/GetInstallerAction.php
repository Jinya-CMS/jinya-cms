<?php

namespace App\Web\Actions\Install;

use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * Action to show the installer page
 */
class GetInstallerAction extends InstallAction
{
    /**
     * Renders the installer page
     *
     * @throws Throwable
     */
    protected function action(): Response
    {
        return $this->render('install::set-config', ['data' => []]);
    }
}
