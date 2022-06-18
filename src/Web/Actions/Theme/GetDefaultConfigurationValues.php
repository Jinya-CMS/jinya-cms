<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\Theming;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class GetDefaultConfigurationValues extends ThemeAction
{
    /**
     * @inheritDoc
     * @return Response
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
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
        $config = $theme->getConfigurationValues();

        return $this->respond($config);
    }
}
