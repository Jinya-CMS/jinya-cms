<?php

namespace App\Web\Actions\Theme;

use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Database\ThemeBlogCategory;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/category/{name}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['category'])]
class PutThemeBlogCategoryAction extends ThemeAction
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
        $categoryId = $body['category'];
        $category = BlogCategory::findById($categoryId);
        if (!$category) {
            throw new NoResultException($this->request, 'Category not found');
        }

        $themeCategory = ThemeBlogCategory::findByThemeAndName($themeId, $name);
        if (null !== $themeCategory) {
            $themeCategory->themeId = $themeId;
            $themeCategory->blogCategoryId = $category->getIdAsInt();
            $themeCategory->name = $name;
            $themeCategory->update();
        } else {
            $themeCategory = new ThemeBlogCategory();
            $themeCategory->themeId = $themeId;
            $themeCategory->blogCategoryId = $category->getIdAsInt();
            $themeCategory->name = $name;
            $themeCategory->create();
        }

        return $this->noContent();
    }
}
