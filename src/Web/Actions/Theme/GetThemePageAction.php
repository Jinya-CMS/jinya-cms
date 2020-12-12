<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use stdClass;

class GetThemePageAction extends ThemeAction
{

    /**
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        $themeId = $this->args['id'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $pages = $theme->getPages();
        $result = [];

        foreach ($pages as $key => $page) {
            $result[$key] = $page->format();
        }

        if (empty($result)) {
            $result = new stdClass();
        }

        return $this->respond($result);
    }
}