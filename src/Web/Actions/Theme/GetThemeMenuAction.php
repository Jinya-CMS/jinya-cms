<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetThemeMenuAction extends ThemeAction
{

    /**
     * @return Response
     * @throws NoResultException
     * @throws JsonException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        $themeId = $this->args['id'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $menus = $theme->getMenus();

        return $this->respond($this->formatIterator($menus));
    }
}