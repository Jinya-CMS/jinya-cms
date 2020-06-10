<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Theme;
use App\Database\ThemeFile;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutThemeFileAction extends Action
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
        $fileId = $body['file'];
        $file = File::findById($fileId);
        if (!$file) {
            throw new NoResultException($this->request, 'File not found');
        }

        $themeFile = ThemeFile::findByThemeAndName($themeId, $name);
        $themeFile->themeId = $themeId;
        $themeFile->fileId = $file;
        $themeFile->name = $name;
        $themeFile->update();

        return $this->noContent();
    }
}