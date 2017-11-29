<?php

namespace DesignerBundle\Controller;

use DataBundle\Entity\Artwork;
use DataBundle\Entity\ArtworkPosition;
use DataBundle\Services\Galleries\GalleryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use const PHP_INT_MAX;
use function array_values;

class GalleryController extends Controller
{
    /**
     * @Route("/gallery/", name="designer_gallery_index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        /** @var GalleryServiceInterface $galleryService */
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $galleries = $galleryService->getAll(0, PHP_INT_MAX, '');

        return $this->render('@Designer/gallery/index.html.twig', [
            'galleries' => $galleries
        ]);
    }

    /**
     * @Route("/gallery/{gallerySlug}", name="designer_gallery_details")
     *
     * @param Request $request
     * @param string $gallerySlug
     * @return Response
     */
    public function detailsAction(Request $request, string $gallerySlug): Response
    {
        /** @var GalleryServiceInterface $galleryService */
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $gallery = $galleryService->get($gallerySlug);

        if ($gallery === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('@Designer/gallery/details.html.twig', [
            'gallery' => $gallery
        ]);
    }

    /**
     * @Route("/gallery/{gallerySlug}/images", name="designer_gallery_images", methods={"GET"})
     *
     * @param Request $request
     * @param string $gallerySlug
     * @return Response
     */
    public function galleryImagesAction(Request $request, string $gallerySlug): Response
    {
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $gallery = $galleryService->get($gallerySlug);

        if ($gallery === null) {
            throw $this->createNotFoundException();
        }

        $artworks = $gallery->getArtworks();
        $result = [];

        /** @var ArtworkPosition $artwork */
        foreach ($artworks as $artwork) {
            /** @var Artwork $currentArtwork */
            $currentArtwork = $artwork->getArtwork();
            $result[$artwork->getPosition()] = [
                'source' => $currentArtwork->getPicture(),
                'name' => $currentArtwork->getName(),
                'description' => $currentArtwork->getDescription(),
                'slug' => $currentArtwork->getSlug(),
                'position' => $artwork->getPosition()
            ];
        }

        uasort($result, function ($a, $b) {
            return ($a['position'] < $b['position']) ? -1 : 1;
        });

        return $this->json(array_values($result));
    }

    /**
     * @Route("/gallery/{gallerySlug}/images/{artworkSlug}", name="designer_gallery_images_position_save", methods={"POST"})
     *
     * @param Request $request
     * @param string $gallerySlug
     * @param string $artworkSlug
     * @return Response
     */
    public function saveGalleryImageAction(Request $request, string $gallerySlug, string $artworkSlug): Response
    {
        $position = $request->get('position', -1);

        $artworkPositionService = $this->get('jinya_gallery.services.artwork_position_service');
        $artworkPositionService->savePosition($gallerySlug, $artworkSlug, $position);

        return $this->json(['success' => true]);
    }

    /**
     * @Route("/gallery/{gallerySlug}/images/{artworkPositionId}", name="designer_gallery_images_position_update", methods={"PUT"})
     *
     * @param Request $request
     * @param string $gallerySlug
     * @param int $artworkPositionId
     * @return Response
     */
    public function updateGalleryImageAction(Request $request, string $gallerySlug, int $artworkPositionId): Response
    {
        $newPosition = $request->get('newPosition', -1);

        $artworkPositionService = $this->get('jinya_gallery.services.artwork_position_service');
        $artworkPositionService->updatePosition($gallerySlug, $artworkPositionId, $newPosition);

        return $this->json(['success' => true]);
    }
}
