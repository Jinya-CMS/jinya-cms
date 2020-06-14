<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Menu;
use App\Database\Theme;
use App\Database\ThemeMenu;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutThemeMenuAction extends ThemeAction
{

    /**
     * @return Response
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws JsonException
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
        $menuId = $body['menu'];
        $menu = Menu::findById($menuId);
        if (!$menu) {
            throw new NoResultException($this->request, 'Menu not found');
        }

        $themeMenu = ThemeMenu::findByThemeAndName($themeId, $name);
        $themeMenu->themeId = $themeId;
        $themeMenu->menuId = $menu;
        $themeMenu->name = $name;
        $themeMenu->update();

        return $this->noContent();
    }
}