<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.01.2018
 * Time: 20:55
 */

namespace ServiceBundle\Services\Routing;


use DataBundle\Services\Artworks\ArtworkServiceInterface;
use DataBundle\Services\Form\FormServiceInterface;
use DataBundle\Services\Galleries\GalleryServiceInterface;
use DataBundle\Services\Pages\PageServiceInterface;
use Symfony\Component\Routing\Router;
use const PHP_INT_MAX;
use function array_map;

class RouteRetrievalService implements RouteRetrievalServiceInterface
{

    /**
     * @var PageServiceInterface
     */
    private $pageService;
    /**
     * @var FormServiceInterface
     */
    private $formService;
    /**
     * @var GalleryServiceInterface
     */
    private $galleryService;
    /**
     * @var ArtworkServiceInterface
     */
    private $artworkService;
    /**
     * @var Router
     */
    private $router;

    /**
     * RouteRetrievalService constructor.
     * @param PageServiceInterface $pageService
     * @param FormServiceInterface $formService
     * @param GalleryServiceInterface $galleryService
     * @param ArtworkServiceInterface $artworkService
     * @param Router $router
     */
    public function __construct(PageServiceInterface $pageService, FormServiceInterface $formService, GalleryServiceInterface $galleryService, ArtworkServiceInterface $artworkService, Router $router)
    {
        $this->pageService = $pageService;
        $this->formService = $formService;
        $this->galleryService = $galleryService;
        $this->artworkService = $artworkService;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function retrieveRoutesByType(string $type): array
    {
        switch ($type) {
            case 'page':
                return $this->retrievePages();
            case 'artwork':
                return $this->retrieveArtworks();
            case 'gallery':
                return $this->retrieveGalleries();
            case 'form':
                return $this->retrieveForms();
            default:
                return [];
        }
    }

    private function retrievePages(): array
    {
        $pages = $this->pageService->getAll(0, PHP_INT_MAX);

        return array_map($this->generateRouteEntry(RouteRetrievalServiceInterface::PAGE_DETAIL_ROUTE), $pages);
    }

    private function generateRouteEntry(string $route)
    {
        return function ($item) use ($route) {
            return [
                'name' => $route,
                'url' => $this->router->generate($route, ['slug' => $item->getSlug()]),
                'parameter' => [
                    'slug' => $item->getSlug()
                ]
            ];
        };
    }

    private function retrieveArtworks(): array
    {
        $artworks = $this->artworkService->getAll(0, PHP_INT_MAX);

        return array_map($this->generateRouteEntry(RouteRetrievalServiceInterface::ARTWORK_DETAIL_ROUTE), $artworks);
    }

    private function retrieveGalleries(): array
    {
        $galleries = $this->galleryService->getAll(0, PHP_INT_MAX);

        return array_map($this->generateRouteEntry(RouteRetrievalServiceInterface::GALLERY_DETAIL_ROUTE), $galleries);
    }

    private function retrieveForms(): array
    {
        $forms = $this->formService->getAll(0, PHP_INT_MAX);

        return array_map($this->generateRouteEntry(RouteRetrievalServiceInterface::FORM_DETAIL_ROUTE), $forms);
    }
}