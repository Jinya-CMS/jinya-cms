<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\Database\Theme;
use App\Database\ThemeGallery;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/gallery/{name}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class PutThemeGalleryAction extends ThemeAction
{
    /**
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
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
        $galleryId = $body['gallery'];
        $gallery = Gallery::findById($galleryId);
        if (!$gallery) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        $themeGallery = ThemeGallery::findByThemeAndName($themeId, $name);
        if (null !== $themeGallery) {
            $themeGallery->themeId = $themeId;
            $themeGallery->galleryId = $gallery->id;
            $themeGallery->name = $name;
            $themeGallery->update();
        } else {
            $themeGallery = new ThemeGallery();
            $themeGallery->themeId = $themeId;
            $themeGallery->galleryId = $gallery->id;
            $themeGallery->name = $name;
            $themeGallery->create();
        }

        return $this->noContent();
    }
}
