<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\Theming;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get the theme preview image
 */
class GetPreviewImageAction extends ThemeAction
{
    /**
     * Gets the theme preview image, if no preview image is configured a 204 is returned
     *
     * @return Response
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws InvalidQueryException
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

        $theme = new Theming\Theme($dbTheme);
        if (file_exists($theme->getPreviewImagePath())) {
            return $this->response
                ->withBody(Stream::create(fopen($theme->getPreviewImagePath(), 'rb+') ?: ''))
                ->withHeader('Content-Type', mime_content_type($theme->getPreviewImagePath()) ?: 'application/octet-stream')
                ->withStatus(self::HTTP_OK);
        }

        return $this->noContent();
    }
}
