<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\Database\Theme;
use App\Database\ThemeSegmentPage;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutThemeSegmentPageAction extends ThemeAction
{

    /**
     * @return Response
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        $themeId = $this->args['id'];
        $name = $this->args['name'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $segmentPageId = $body['segmentPage'];
        $segmentPage = SegmentPage::findById($segmentPageId);
        if (!$segmentPage) {
            throw new NoResultException($this->request, 'SegmentPage not found');
        }

        $themeSegmentPage = ThemeSegmentPage::findByThemeAndName($themeId, $name);
        $themeSegmentPage->themeId = $themeId;
        $themeSegmentPage->segmentPageId = $segmentPage;
        $themeSegmentPage->name = $name;
        $themeSegmentPage->update();

        return $this->noContent();
    }
}