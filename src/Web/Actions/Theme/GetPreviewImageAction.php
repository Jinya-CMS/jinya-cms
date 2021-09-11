<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\Theming;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/preview', JinyaAction::GET)]
class GetPreviewImageAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws JsonException
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
                ->withBody(Stream::create(fopen($theme->getPreviewImagePath(), 'rb')))
                ->withHeader('Content-Type', 'application/octet-stream')
                ->withStatus(self::HTTP_OK);
        }

        return $this->noContent();
    }
}
