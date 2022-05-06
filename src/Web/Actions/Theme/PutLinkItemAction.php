<?php

namespace App\Web\Actions\Theme;

use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

class PutLinkItemAction extends ThemeAction
{

    /**
     * @inheritDoc
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

        $body = $this->request->getParsedBody();
        $id = $body[$entityType];

        $reflectionClass = new \ReflectionClass('App\Database\Theme' . ucfirst($entityType));
        $themeLink = $reflectionClass->getMethod('findByThemeAndName')->invoke(null, $themeId, $name);
        if (null !== $themeLink) {
            $themeLink->themeId = $themeId;
            $themeLink->{$field} = $id;
            $themeLink->name = $name;
            $themeLink->update();
        } else {
            $themeLink = $reflectionClass->newInstance();
            $themeLink->themeId = $themeId;
            $themeLink->$field = $id;
            $themeLink->name = $name;
            $themeLink->create();
        }

        return $this->noContent();
    }
}