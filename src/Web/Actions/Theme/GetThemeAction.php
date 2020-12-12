<?php

namespace App\Web\Actions\Theme;

use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetThemeAction extends ThemeAction
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $theme = Theme::findById($themeId);
        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        return $this->respond($theme->format());
    }
}