<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\Database\Theme;
use App\Database\ThemePage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutThemePageAction extends Action
{

    /**
     * @return Response
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $name = $this->args['name'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $pageId = $body['page'];
        $page = SimplePage::findById($pageId);
        if (!$page) {
            throw new NoResultException($this->request, 'Page not found');
        }

        $themePage = ThemePage::findByThemeAndName($themeId, $name);
        $themePage->themeId = $themeId;
        $themePage->pageId = $page;
        $themePage->name = $name;
        $themePage->update();

        return $this->noContent();
    }
}