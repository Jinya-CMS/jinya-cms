<?php

namespace App\Web\Actions\Menu;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Menu;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateMenuAction extends Action
{
    /**
     * @inheritDoc
     * @throws ConflictException
     * @throws JsonException
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $menu = new Menu();
        $menu->name = $body['name'];
        if (isset($body['logo'])) {
            if (File::findById($body['logo'])) {
                $menu->logo = $body['logo'];
            } else {
                throw new NoResultException($this->request, 'File for logo not found');
            }
        }

        try {
            $menu->create();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->respond($menu->format(), Action::HTTP_CREATED);
    }
}