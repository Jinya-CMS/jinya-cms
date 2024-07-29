<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\File;
use Jinya\Cms\Database\FileTag;
use Jinya\Cms\Database\Folder;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class MediaController extends BaseController
{
    /**
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, 'api/media')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getRootFolder(): ResponseInterface
    {
        $folders = Folder::findRootFolders();
        $files = File::findRootFiles();
        $tags = FileTag::findAll('name ASC');

        $data = [
            'folders' => $this->formatIteratorPlain($folders),
            'files' => $this->formatIteratorPlain($files),
            'tags' => $this->formatIteratorPlain($tags),
        ];

        return $this->json($data);
    }
}
