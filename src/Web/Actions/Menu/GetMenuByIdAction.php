<?php

namespace App\Web\Actions\Menu;

use App\Database\Menu;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetMenuByIdAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $menu = Menu::findById($id);
        if ($menu === null) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        return $this->respond($menu->format());
    }
}