<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Theme;
use App\Database\ThemeFile;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutThemeFileAction extends ThemeAction
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
        $name = $this->args['name'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $fileId = $body['file'];
        $file = File::findById($fileId);
        if (!$file) {
            throw new NoResultException($this->request, 'File not found');
        }

        $themeFile = ThemeFile::findByThemeAndName($themeId, $name);
        if ($themeFile !== null) {
            $themeFile->themeId = $themeId;
            $themeFile->fileId = $file->id;
            $themeFile->name = $name;
            $themeFile->update();
        } else {
            $themeFile = new ThemeFile();
            $themeFile->themeId = $themeId;
            $themeFile->fileId = $file->id;
            $themeFile->name = $name;
            $themeFile->create();
        }

        return $this->noContent();
    }
}