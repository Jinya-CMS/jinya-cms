<?php

namespace Jinya\Controller\Designer;

use Jinya\Entity\Artwork;
use Jinya\Entity\ArtworkPosition;
use Jinya\Framework\BaseController;
use Jinya\Services\Artworks\ArtworkPositionServiceInterface;
use Jinya\Services\Galleries\GalleryServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use const PHP_INT_MAX;
use function array_values;

class GalleryController extends BaseController
{
    /**
     * @Route("/designer/gallery/", name="designer_gallery_index")
     *
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function indexAction(GalleryServiceInterface $galleryService): Response
    {
        $galleries = $galleryService->getAll(0, PHP_INT_MAX, '');

        return $this->render('@Designer/Gallery/index.html.twig', [
            'galleries' => $galleries
        ]);
    }

    /**
     * @Route("/designer/gallery/{gallerySlug}", name="designer_gallery_details")
     *
     * @param string $gallerySlug
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function detailsAction(string $gallerySlug, GalleryServiceInterface $galleryService): Response
    {
        $gallery = $galleryService->get($gallerySlug);

        if ($gallery === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('@Designer/Gallery/details.html.twig', [
            'gallery' => $gallery
        ]);
    }

    /**
     * @Route("/designer/gallery/{gallerySlug}/images", name="designer_gallery_images", methods={"GET"})
     *
     * @param string $gallerySlug
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function galleryImagesAction(string $gallerySlug, GalleryServiceInterface $galleryService): Response
    {
        try {
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
                    'position' => $artwork->getPosition(),
                    'id' => $artwork->getId()
                ];
            }

            uasort($result, function ($a, $b) {
                return ($a['position'] < $b['position']) ? -1 : 1;
            });

            return $this->json(array_values($result));
        } catch (Throwable $exception) {
            return $this->jsonError($exception);
        }
    }

    /**
     * @param $exception
     * @return Response
     */
    private function jsonError(Throwable $exception): Response
    {
        return $this->json([
            'message' => $exception->getMessage(),
            'success' => false
        ], 500);
    }

    /**
     * @Route("/designer/gallery/{gallerySlug}/images/{artworkSlug}", name="designer_gallery_images_position_save", methods={"POST"})
     *
     * @param Request $request
     * @param string $gallerySlug
     * @param string $artworkSlug
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function saveGalleryImageAction(Request $request, string $gallerySlug, string $artworkSlug, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        try {
            $position = $request->get('position', -1);
            $artworkPositionService->savePosition($gallerySlug, $artworkSlug, $position);

            return $this->json(['success' => true]);
        } catch (Throwable $exception) {
            return $this->jsonError($exception);
        }
    }

    /**
     * @Route("/designer/gallery/{gallerySlug}/images/{artworkSlug}", name="designer_gallery_images_position_change_image", methods={"PUT"})
     *
     * @param Request $request
     * @param string $artworkSlug
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function updateGalleryImageAction(Request $request, string $artworkSlug, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        try {
            $id = (int)$request->get('id');

            $artworkPositionService->updateArtwork($id, $artworkSlug);

            return $this->json(['success' => true]);
        } catch (Throwable $exception) {
            return $this->jsonError($exception);
        }
    }

    /**
     * @Route("/designer/gallery/{gallerySlug}/{artworkPositionId}", name="designer_gallery_images_position_update", methods={"PUT"})
     *
     * @param Request $request
     * @param string $gallerySlug
     * @param int $artworkPositionId
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function updateGalleryImagePositionAction(Request $request, string $gallerySlug, int $artworkPositionId, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        try {
            $newPosition = $request->get('newPosition', -1);
            $artworkPositionService->updatePosition($gallerySlug, $artworkPositionId, $newPosition);

            return $this->json(['success' => true]);
        } catch (Throwable $exception) {
            return $this->jsonError($exception);
        }
    }

    /**
     * @Route("/designer/gallery/{gallerySlug}/images/{id}", name="designer_gallery_images_position_delete")
     *
     * @param int $id
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function deleteGalleryImageAction(int $id, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        try {
            $artworkPositionService->deletePosition($id);

            return $this->json(['success' => true]);
        } catch (Throwable $exception) {
            return $this->jsonError($exception);
        }
    }
}
