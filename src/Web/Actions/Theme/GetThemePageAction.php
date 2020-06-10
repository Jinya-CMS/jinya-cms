<?php

namespace App\Web\Actions\Theme;

use App\Database\Theme;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetThemePageAction extends Action
{

    /**
     * @return Response
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $pages = $theme->getPages();

        return $this->respond($this->formatIterator($pages));
    }
}