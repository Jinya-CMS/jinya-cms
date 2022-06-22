<?php

namespace App\Web\Actions\Update;

use App\Database\Migrations\Migrator;
use App\Web\Actions\Action;
use JsonException;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use ZipArchive;

/**
 * Action to post the update
 */
class PostUpdateAction extends UpdateAction
{
    /**
     * Executes the update, first the latest version will be loaded from the configured release server, unpacks the zip and replaces the files
     *
     * @return ResponseInterface
     * @throws JsonException
     * @throws Throwable
     */
    protected function action(): ResponseInterface
    {

        if (isset($this->body['cancel'])) {
            unlink(__ROOT__ . '/update.lock');
        }

        if (isset($this->body['update'])) {
            try {
                $updatePath = __ROOT__ . '/var/update.zip';
                $newVersion = $this->body['newVersion'];
                $releasePath = $this->getReleasePath($newVersion);
                copy($releasePath, __ROOT__ . '/var/update.zip');
                $zipStream = new ZipArchive();
                $zipStream->open($updatePath);
                $zipStream->extractTo(__ROOT__);
                $zipStream->close();
                Migrator::migrate();
                unlink(__ROOT__ . '/update.lock');
            } catch (Throwable $exception) {
                return $this->render(
                    'update::update',
                    [
                        'newVersion' => array_key_last($this->getReleases()),
                        'version' => INSTALLED_VERSION,
                        'error' => $exception->getMessage() . '<br>' . $exception->getTraceAsString(),
                    ],
                );
            }
        }

        return (new Response())
            ->withStatus(Action::HTTP_MOVED_PERMANENTLY)
            ->withHeader('Location', '/designer');
    }
}
