<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use ReflectionClass;
use ReflectionException;

/**
 *
 */
class PutLinkItemAction extends ThemeAction
{

    /**
     * @inheritDoc
     * @throws ReflectionException
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
        $field = $entityType . 'Id';
        $themeId = $this->args['id'];
        $name = $this->args['name'];

        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $id = $this->body[$entityType];

        /**
         * @phpstan-ignore-next-line
         */
        $reflectionClass = new ReflectionClass('App\Database\Theme' . ucfirst($entityType));
        $themeLink = $reflectionClass->getMethod('findByThemeAndName')->invoke(null, $themeId, $name);
        if ($themeLink !== null) {
            $themeLink->themeId = $themeId;
            $themeLink->$field = $id;
            $themeLink->name = $name;
            $themeLink->update();
        } else {
            $themeLink = $reflectionClass->newInstance();
            /**
             * @phpstan-ignore-next-line
             */
            $themeLink->themeId = $themeId;
            $themeLink->$field = $id;
            /**
             * @phpstan-ignore-next-line
             */
            $themeLink->name = $name;
            /**
             * @phpstan-ignore-next-line
             */
            $themeLink->create();
        }

        return $this->noContent();
    }
}
