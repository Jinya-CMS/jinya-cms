<?php

namespace App\Web\Actions\Update;

use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Throwable;
use ZipArchive;

class PostUpdateAction extends UpdateAction
{

    /**
     * @inheritDoc
     */
    protected function action(): ResponseInterface
    {
        $body = $this->request->getParsedBody();
        if (isset($body['cancel'])) {
            unlink(__ROOT__ . '/update.lock');
        }

        if (isset($body['update'])) {
            try {
                $updatePath = __ROOT__ . '/var/update.zip';
                $newVersion = $body['newVersion'];
                $releasePath = $this->getReleasePath($newVersion);
                copy($releasePath, __ROOT__ . '/var/update.zip');
                $zipStream = new ZipArchive();
                $zipStream->open($updatePath);
                $zipStream->extractTo(__ROOT__);
                $zipStream->close();
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