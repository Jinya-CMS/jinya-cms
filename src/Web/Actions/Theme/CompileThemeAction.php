<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\Theming;
use App\Web\Exceptions\NoResultException;
use Exception;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use ScssPhp\ScssPhp\Exception\SassException;

class CompileThemeAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws InvalidQueryException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     * @throws SassException
     * @throws Exception
     * @throws Exception
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $dbTheme = Database\Theme::findById($themeId);
        if (!$dbTheme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        /** @noinspection PhpParamsInspection */
        $theme = new Theming\Theme($dbTheme);
        $theme->compileAssetCache();
        $theme->compileStyleCache();
        $theme->compileScriptCache();

        return $this->noContent();
    }
}
