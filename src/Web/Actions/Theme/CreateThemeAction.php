<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Theming\ThemeSyncer;
use App\Utils\UuidGenerator;
use App\Web\Exceptions\NoResultException;
use Exception;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use ZipArchive;

/**
 * Action to upload a theme
 */
class CreateThemeAction extends ThemeAction
{
    /**
     * Uploads a new theme, unzips the posted body and syncs all themes
     *
     * @return Response
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     * @throws Exception
     */
    protected function action(): Response
    {
        $themeName = $this->request->getQueryParams()['name'];
        if ($themeName) {
            $tmpFile = __JINYA_TEMP . UuidGenerator::generateV4();
            file_put_contents($tmpFile, $this->request->getBody()->detach());

            $zipArchive = new ZipArchive();
            $zipArchive->open($tmpFile);
            $zipArchive->extractTo(ThemeSyncer::THEME_BASE_PATH . $themeName);
            $this->syncThemes();

            unlink($tmpFile);
        }

        return $this->noContent();
    }
}
