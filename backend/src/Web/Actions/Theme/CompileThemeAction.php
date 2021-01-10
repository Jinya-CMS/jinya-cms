<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\Theming;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/assets', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class CompileThemeAction extends ThemeAction
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     * @throws Exception
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $dbTheme = Database\Theme::findById($themeId);
        if (!$dbTheme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $theme = new Theming\Theme($dbTheme);
        $theme->compileStyleCache();
        $theme->compileScriptCache();
        $theme->compileAssetCache();

        return $this->noContent();
    }
}