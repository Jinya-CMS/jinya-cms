<?php

namespace App\Web\Actions\Menu;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Menu;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu/{id}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class UpdateMenuAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
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
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->noContent();
    }
}