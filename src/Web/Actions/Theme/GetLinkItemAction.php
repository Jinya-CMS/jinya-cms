<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use stdClass;

/**
 * Action to get the links by entity type
 */
class GetLinkItemAction extends ThemeAction
{
    /**
     * Gets the links by entity type and theme
     *
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $entityType = $this->args['entityType'];
        if ($entityType === 'segment-page') {
            $entityType = 'segmentPage';
        } elseif ($entityType === 'category') {
            $entityType = 'blogCategory';
        }
        $themeId = $this->args['id'];

        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $method = 'get' . ucfirst($entityType) . 's';
        if ($entityType === 'gallery') {
            $method = 'getGalleries';
        } elseif ($entityType === 'blogCategory') {
            $method = 'getCategories';
        }
        $links = $theme->{$method}();
        $result = [];

        foreach ($links as $key => $link) {
            $result[$key] = $link->format();
        }
        if (empty($result)) {
            $result = new stdClass();
        }

        return $this->respond($result);
    }
}
