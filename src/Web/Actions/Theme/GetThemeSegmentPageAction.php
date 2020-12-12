<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use stdClass;

class GetThemeSegmentPageAction extends ThemeAction
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

        $segmentPages = $theme->getSegmentPages();
        $result = [];

        foreach ($segmentPages as $key => $segmentPage) {
            $result[$key] = $segmentPage->format();
        }
        if (empty($result)) {
            $result = new stdClass();
        }

        return $this->respond($result);
    }
}