<?php

namespace App\Web\Actions\Update;

use App\Database\Migrations\Migrator;
use Psr\Http\Message\ResponseInterface as Response;
use ZipArchive;

class ApiUpdateAction extends UpdateAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $updatePath = __ROOT__ . '/var/update.zip';
        $newVersion = array_key_last($this->getReleases());
        $releasePath = $this->getReleasePath($newVersion);
        copy($releasePath, __ROOT__ . '/var/update.zip');
        $zipStream = new ZipArchive();
        $zipStream->open($updatePath);
        $zipStream->extractTo(__ROOT__);
        $zipStream->close();
        Migrator::migrate();
    }
}