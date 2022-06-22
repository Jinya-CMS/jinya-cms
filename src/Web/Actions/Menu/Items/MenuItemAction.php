<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Artist;
use App\Database\BlogCategory;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\MenuItem;
use App\Database\SegmentPage;
use App\Database\SimplePage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;

/**
 * Base action for menu items
 */
abstract class MenuItemAction extends Action
{
    /**
     * Fills the menu item with the data from the body
     *
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    public function fillMenuItem(MenuItem $menuItem = null): MenuItem
    {
        if (!$menuItem) {
            $menuItem = new MenuItem();
        }

        if (isset($this->body['route'])) {
            $menuItem->route = $this->body['route'];
        }

        if (isset($this->body['artist'])) {
            $artist = Artist::findById($this->body['artist']);
            if (!$artist) {
                throw new NoResultException($this->request, 'Artist not found');
            }

            $menuItem->artistId = $artist->getIdAsInt();
            $menuItem->formId = null;
            $menuItem->pageId = null;
            $menuItem->segmentPageId = null;
            $menuItem->galleryId = null;
            $menuItem->categoryId = null;
            $menuItem->blogHomePage = false;
        } elseif (isset($this->body['form'])) {
            $form = Form::findById($this->body['form']);
            if (!$form) {
                throw new NoResultException($this->request, 'Form not found');
            }

            $menuItem->formId = $form->getIdAsInt();
            $menuItem->artistId = null;
            $menuItem->pageId = null;
            $menuItem->segmentPageId = null;
            $menuItem->galleryId = null;
            $menuItem->categoryId = null;
            $menuItem->blogHomePage = false;
        } elseif (isset($this->body['page'])) {
            $page = SimplePage::findById($this->body['page']);
            if (!$page) {
                throw new NoResultException($this->request, 'Page not found');
            }

            $menuItem->pageId = $page->getIdAsInt();
            $menuItem->artistId = null;
            $menuItem->formId = null;
            $menuItem->segmentPageId = null;
            $menuItem->galleryId = null;
            $menuItem->categoryId = null;
            $menuItem->blogHomePage = false;
        } elseif (isset($this->body['segmentPage'])) {
            $segmentPage = SegmentPage::findById($this->body['segmentPage']);
            if (!$segmentPage) {
                throw new NoResultException($this->request, 'Segment page not found');
            }

            $menuItem->segmentPageId = $segmentPage->getIdAsInt();
        } elseif (isset($this->body['gallery'])) {
            $gallery = Gallery::findById($this->body['gallery']);
            if (!$gallery) {
                throw new NoResultException($this->request, 'Gallery not found');
            }

            $menuItem->galleryId = $gallery->getIdAsInt();
            $menuItem->artistId = null;
            $menuItem->pageId = null;
            $menuItem->segmentPageId = null;
            $menuItem->formId = null;
            $menuItem->categoryId = null;
            $menuItem->blogHomePage = false;
        } elseif (isset($this->body['category'])) {
            $category = BlogCategory::findById($this->body['category']);
            if (!$category) {
                throw new NoResultException($this->request, 'Category not found');
            }

            $menuItem->categoryId = $category->getIdAsInt();
            $menuItem->artistId = null;
            $menuItem->pageId = null;
            $menuItem->segmentPageId = null;
            $menuItem->formId = null;
            $menuItem->galleryId = null;
            $menuItem->blogHomePage = false;
        }

        if (isset($this->body['blogHomePage'])) {
            $menuItem->blogHomePage = $this->body['blogHomePage'];
        }

        if (isset($this->body['position'])) {
            $menuItem->position = $this->body['position'];
        }

        if (isset($this->body['title'])) {
            $menuItem->title = $this->body['title'];
        }

        if (isset($this->body['highlighted'])) {
            $menuItem->highlighted = $this->body['highlighted'];
        }

        return $menuItem;
    }

    /**
     * Formats an iterator recursively
     *
     * @return array<mixed>
     */
    protected function formatIteratorRecursive(Iterator $iterator): array
    {
        $data = [];
        foreach ($iterator as $item) {
            $data[] = $item->formatRecursive();
        }

        return $data;
    }
}
