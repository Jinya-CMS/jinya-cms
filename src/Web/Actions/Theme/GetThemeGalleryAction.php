<?php

namespace App\Web\Actions\Theme;

use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use stdClass;

class GetThemeGalleryAction extends ThemeAction
{

    /**
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

        $galleries = $theme->getGalleries();
        $result = [];

        foreach ($galleries as $key => $gallery) {
            $result[$key] = $gallery->format();
        }
        if (empty($result)) {
            $result = new stdClass();
        }

        return $this->respond($result);
    }
}