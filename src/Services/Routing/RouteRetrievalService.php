<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.01.2018
 * Time: 20:55
 */

namespace Jinya\Services\Routing;

use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Jinya\Services\Pages\PageServiceInterface;
use Symfony\Component\Routing\RouterInterface;
use function array_map;
use const PHP_INT_MAX;

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
     * @var ArtGalleryServiceInterface
     */
    private $galleryService;

    /**
     * @var ArtworkServiceInterface
     */
    private $artworkService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * RouteRetrievalService constructor.
     * @param PageServiceInterface $pageService
     * @param FormServiceInterface $formService
     * @param ArtGalleryServiceInterface $galleryService
     * @param ArtworkServiceInterface $artworkService
     * @param RouterInterface $router
     */
    public function __construct(PageServiceInterface $pageService, FormServiceInterface $formService, ArtGalleryServiceInterface $galleryService, ArtworkServiceInterface $artworkService, RouterInterface $router)
    {
        $this->pageService = $pageService;
        $this->formService = $formService;
        $this->galleryService = $galleryService;
        $this->artworkService = $artworkService;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveRoutesByType(string $type): array
    {
        switch ($type) {
            case RouteRetrievalServiceInterface::PAGE_DETAIL_ROUTE:
                return $this->retrievePages();
            case RouteRetrievalServiceInterface::ARTWORK_DETAIL_ROUTE:
                return $this->retrieveArtworks();
            case RouteRetrievalServiceInterface::GALLERY_DETAIL_ROUTE:
                return $this->retrieveGalleries();
            case RouteRetrievalServiceInterface::FORM_DETAIL_ROUTE:
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
        return function (/* @var \Jinya\Entity\Base\SlugEntity $item */
            $item) use ($route) {
            return [
                'name' => $route,
                'url' => $this->router->generate($route, ['slug' => $item->getSlug()]),
                'parameter' => [
                    'slug' => $item->getSlug(),
                ],
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
