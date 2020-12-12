<?php

namespace App\Web\Actions\Menu;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Menu;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateMenuAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $menu = Menu::findById($id);
        if ($menu === null) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        if (isset($body['name'])) {
            $menu->name = $body['name'];
        }

        if (isset($body['logo'])) {
            if (File::findById($body['logo'])) {
                $menu->logo = $body['logo'];
            } else {
                throw new NoResultException($this->request, 'File for logo not found');
            }
        }

        try {
            $menu->update();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->noContent();
    }
}