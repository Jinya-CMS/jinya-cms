<?php

namespace App\Web\Actions\Menu;

use App\Database\Menu;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteMenuAction extends Action
{

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $menu = Menu::findById($this->args['id']);
        if ($menu === null) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        $menu->delete();

        return $this->noContent();
    }
}