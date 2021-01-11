<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\Database\Theme;
use App\Database\ThemePage;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/page/{name}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class PutThemePageAction extends ThemeAction
{
    /**
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $this->syncThemes();
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
        if (null !== $themePage) {
            $themePage->themeId = $themeId;
            $themePage->pageId = $page->id;
            $themePage->name = $name;
            $themePage->update();
        } else {
            $themePage = new ThemePage();
            $themePage->themeId = $themeId;
            $themePage->pageId = $page->id;
            $themePage->name = $name;
            $themePage->create();
        }

        return $this->noContent();
    }
}
