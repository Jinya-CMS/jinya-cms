<?php

namespace App\Web\Actions\Gallery;

use App\Database\Gallery;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteGalleryAction extends Action
{

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $page = Gallery::findBySlug($this->args['slug']);
        if ($page === null) {
            throw new NoResultException($this->request, 'Page not found');
        }

        $page->delete();

        return $this->noContent();
    }
}