<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Theming\ThemeSyncer;
use App\Utils\UuidGenerator;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Exception;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use ZipArchive;

/**
 *
 */
class UpdateThemeFilesAction extends Action
{

    /**
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
        /** @var Theme $theme */
        $theme = Theme::findById($this->args['id']);
        if ($theme) {
            $tmpFile = __JINYA_TEMP . UuidGenerator::generateV4();
            file_put_contents($tmpFile, $this->request->getBody()->detach());

            $zipArchive = new ZipArchive();
            $zipArchive->open($tmpFile);
            $zipArchive->extractTo(ThemeSyncer::THEME_BASE_PATH . $theme->name);
            $themeSyncer = new ThemeSyncer();
            $themeSyncer->syncThemes();

            unlink($tmpFile);
        } else {
            throw new NoResultException($this->request, 'Theme not found');
        }

        return $this->noContent();
    }
}