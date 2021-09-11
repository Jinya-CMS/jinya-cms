<?php

namespace App\Web\Actions\Menu;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Menu;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/menu/{id}', JinyaAction::DELETE)]
#[Authenticated(Authenticated::WRITER)]
class DeleteMenuAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $menu = Menu::findById($this->args['id']);
        if (null === $menu) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        $menu->delete();

        return $this->noContent();
    }
}
