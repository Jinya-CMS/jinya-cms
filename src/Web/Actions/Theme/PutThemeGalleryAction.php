<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\Database\Theme;
use App\Database\ThemeGallery;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutThemeGalleryAction extends Action
{

    /**
     * @return Response
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $name = $this->args['name'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $galleryId = $body['gallery'];
        $gallery = Gallery::findById($galleryId);
        if (!$gallery) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        $themeGallery = ThemeGallery::findByThemeAndName($themeId, $name);
        $themeGallery->themeId = $themeId;
        $themeGallery->galleryId = $gallery;
        $themeGallery->name = $name;
        $themeGallery->update();

        return $this->noContent();
    }
}