<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetThemeSegmentPageAction extends ThemeAction
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

        $segmentPages = $theme->getSegmentPages();

        return $this->respond($this->formatIterator($segmentPages));
    }
}