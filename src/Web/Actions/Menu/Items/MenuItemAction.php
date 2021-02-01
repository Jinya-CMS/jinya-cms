<?php

namespace App\Web\Actions\Menu\Items;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\MenuItem;
use App\Database\SegmentPage;
use App\Database\SimplePage;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Iterator;

abstract class MenuItemAction extends Action
{
    public const MENU_ITEM_EXAMPLE = [
        'id' => 0,
        'position' => 0,
        'highlighted' => false,
        'title' => OpenApiResponse::FAKER_WORD,
        'route' => OpenApiResponse::FAKER_URL,
    ];
    public const MENU_ITEM_REQUEST_EXAMPLE = [
        'position' => 0,
        'highlighted' => false,
        'title' => OpenApiResponse::FAKER_WORD,
        'route' => OpenApiResponse::FAKER_URL,
        'page' => 0,
    ];
    public const MENU_ITEM_SCHEMA = [
        'title' => ['type' => 'string'],
        'position' => ['type' => 'integer'],
        'artist' => ['type' => 'integer'],
        'form' => ['type' => 'integer'],
        'page' => ['type' => 'integer'],
        'segmentPage' => ['type' => 'integer'],
        'gallery' => ['type' => 'integer'],
    ];

    /**
     * Fills the menu item with the data from the body
     *
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function fillMenuItem(MenuItem $menuItem = null): MenuItem
    {
        if (!$menuItem) {
            $menuItem = new MenuItem();
        }

        $body = $this->request->getParsedBody();
        if (isset($body['route'])) {
            $menuItem->route = $body['route'];
        }

        if (isset($body['artist'])) {
            $artist = Artist::findById($body['artist']);
            if (!$artist) {
                throw new NoResultException($this->request, 'Artist not found');
            }

            $menuItem->artistId = $artist->id;
            $menuItem->formId = null;
            $menuItem->pageId = null;
            $menuItem->segmentPageId = null;
            $menuItem->galleryId = null;
        } elseif (isset($body['form'])) {
            $form = Form::findById($body['form']);
            if (!$form) {
                throw new NoResultException($this->request, 'Form not found');
            }

            $menuItem->formId = $form->id;
            $menuItem->artistId = null;
            $menuItem->pageId = null;
            $menuItem->segmentPageId = null;
            $menuItem->galleryId = null;
        } elseif (isset($body['page'])) {
            $page = SimplePage::findById($body['page']);
            if (!$page) {
                throw new NoResultException($this->request, 'Page not found');
            }

            $menuItem->pageId = $page->id;
            $menuItem->artistId = null;
            $menuItem->formId = null;
            $menuItem->segmentPageId = null;
            $menuItem->galleryId = null;
        } elseif (isset($body['segmentPage'])) {
            $segmentPage = SegmentPage::findById($body['segmentPage']);
            if (!$segmentPage) {
                throw new NoResultException($this->request, 'Segment page not found');
            }

            $menuItem->segmentPageId = $segmentPage->id;
        } elseif (isset($body['gallery'])) {
            $gallery = Gallery::findById($body['gallery']);
            if (!$gallery) {
                throw new NoResultException($this->request, 'Gallery not found');
            }

            $menuItem->galleryId = $gallery->id;
            $menuItem->artistId = null;
            $menuItem->pageId = null;
            $menuItem->segmentPageId = null;
            $menuItem->formId = null;
        }

        if (isset($body['position'])) {
            $menuItem->position = $body['position'];
        }

        if (isset($body['title'])) {
            $menuItem->title = $body['title'];
        }

        if (isset($body['highlighted'])) {
            $menuItem->highlighted = $body['highlighted'];
        }

        return $menuItem;
    }

    /**
     * Formats an iterator recursively
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
