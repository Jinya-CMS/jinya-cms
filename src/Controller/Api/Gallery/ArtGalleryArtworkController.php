<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 23:38
 */

namespace Jinya\Controller\Api\Gallery;

use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Gallery\ArtGalleryFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Artworks\ArtworkPositionServiceInterface;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtGalleryArtworkController extends BaseApiController
{
    /**
     * @Route("/api/gallery/{gallerySlug}/artwork", methods={"GET"}, name="api_gallery_artwork_get")
     *
     * @param string $gallerySlug
     * @param ArtGalleryServiceInterface $galleryService
     * @param ArtGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAction(string $gallerySlug, ArtGalleryServiceInterface $galleryService, ArtGalleryFormatterInterface $galleryFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($gallerySlug, $galleryService, $galleryFormatter) {
            $gallery = $galleryService->get($gallerySlug);

            return $galleryFormatter->init($gallery)->artworks()->format()['artworks'];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{gallerySlug}/artwork", methods={"POST"}, name="api_gallery_artwork_position_post")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $gallerySlug
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function postAction(string $gallerySlug, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($gallerySlug, $artworkPositionService) {
            $position = $this->getValue('position', -1);
            $artworkSlug = $this->getValue('artwork', null);

            if (empty($artworkSlug)) {
                throw new MissingFieldsException(['artwork' => 'api.gallery.field.artworkSlug.missing']);
            }

            return $artworkPositionService->savePosition($gallerySlug, $artworkSlug, $position);
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{gallerySlug}/artwork/{id}", methods={"DELETE"}, name="api_gallery_artwork_position_delete")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int id$
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function deleteAction(int $id, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $artworkPositionService) {
            $artworkPositionService->deletePosition($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{gallerySlug}/artwork/{id}/{oldPosition}", methods={"PUT"}, name="api_gallery_artwork_position_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int $id
     * @param int $oldPosition
     * @param string $gallerySlug
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function putPositionAction(int $id, int $oldPosition, string $gallerySlug, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($gallerySlug, $id, $oldPosition, $artworkPositionService) {
            $newPosition = $this->getValue('position', null);
            $artworkSlug = $this->getValue('artwork', null);

            if (!empty($artworkSlug)) {
                $artworkPositionService->updateArtwork($id, $artworkSlug);
            }
            if (isset($newPosition) && null !== $newPosition) {
                $artworkPositionService->updatePosition($gallerySlug, $id, $oldPosition, $newPosition);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
