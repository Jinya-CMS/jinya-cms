<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\Database\Theme;
use App\Database\ThemeSegmentPage;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/segment-page/{name}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class PutThemeSegmentPageAction extends ThemeAction
{

    /**
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
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
        if ($themeSegmentPage !== null) {
            $themeSegmentPage->themeId = $themeId;
            $themeSegmentPage->segmentPageId = $segmentPage->id;
            $themeSegmentPage->name = $name;
            $themeSegmentPage->update();
        } else {
            $themeSegmentPage = new ThemeSegmentPage();
            $themeSegmentPage->themeId = $themeId;
            $themeSegmentPage->segmentPageId = $segmentPage->id;
            $themeSegmentPage->name = $name;
            $themeSegmentPage->create();
        }

        return $this->noContent();
    }
}