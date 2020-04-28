<?php

namespace App\Web\Actions\SimplePage;

use App\Database\SimplePage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetSimplePageBySlugAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $slug = $this->args['slug'];
        $page = SimplePage::findBySlug($slug);
        if ($page === null) {
            throw new NoResultException($this->request, 'Page not found');
        }

        return $this->respond($page->format());
    }
}