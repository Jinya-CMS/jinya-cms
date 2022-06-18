<?php

namespace App\Web\Actions\File;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Utils\ImageManipulator;
use App\Web\Actions\Action;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

/**
 *
 */
class GetFileContentAction extends Action
{
    /**
     * @inheritDoc
     * @throws HttpNotFoundException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $fileId = $this->args['id'];
        $file = File::findById($fileId);

        if ($file === null) {
            throw new HttpNotFoundException($this->request, 'File not found');
        }

        return $this->respondFile($file->path, $file->type);
    }
}
