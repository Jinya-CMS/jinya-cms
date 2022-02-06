<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Menu;
use App\Database\Theme;
use App\Database\ThemeMenu;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/menu/{name}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class PutThemeMenuAction extends ThemeAction
{
    /**
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
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
        if (null !== $themeMenu) {
            $themeMenu->themeId = $themeId;
            $themeMenu->menuId = $menu->id;
            $themeMenu->name = $name;
            $themeMenu->update();
        } else {
            $themeMenu = new ThemeMenu();
            $themeMenu->themeId = $themeId;
            $themeMenu->menuId = $menu->id;
            $themeMenu->name = $name;
            $themeMenu->create();
        }

        return $this->noContent();
    }
}
