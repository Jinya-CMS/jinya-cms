<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\Theming;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/configuration/structure', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
class GetConfigurationStructureAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $dbTheme = Database\Theme::findById($themeId);
        if (!$dbTheme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $theme = new Theming\Theme($dbTheme);

        return $this->respond($theme->getConfigurationStructure());
    }
}
