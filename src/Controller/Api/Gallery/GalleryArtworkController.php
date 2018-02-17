<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 23:38
 */

namespace Jinya\Controller\Api\Gallery;

use Jinya\Exceptions\MissingFieldsException;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Artworks\ArtworkPositionServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryArtworkController extends BaseApiController
{
    /**
     * @Route("/api/gallery/{gallerySlug}/artwork", methods={"GET"}, name="api_gallery_artwork_get")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param string $gallerySlug
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function getAction(string $gallerySlug, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($gallerySlug, $artworkPositionService) {
            return $artworkPositionService->getArtworks($gallerySlug);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{gallerySlug}/artwork", methods={"POST"}, name="api_gallery_artwork_position_post")
     * @IsGranted("ROLE_WRITER")
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

            $artworkPositionService->savePosition($gallerySlug, $artworkSlug, $position);

            return null;
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{gallerySlug}/artwork/{id}", methods={"DELETE"}, name="api_gallery_artwork_position_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int id$
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function deleteAction(int $id, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $artworkPositionService) {
            $artworkPositionService->deletePosition($id);;
            return null;
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{gallerySlug}/artwork/{id}", methods={"PUT"}, name="api_gallery_artwork_position_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int id$
     * @param string $gallerySlug
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @return Response
     */
    public function putPositionAction(int $id, string $gallerySlug, ArtworkPositionServiceInterface $artworkPositionService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($gallerySlug, $id, $artworkPositionService) {
            $newPosition = $this->getValue('position', null);
            $artworkSlug = $this->getValue('artwork', null);

            if (!empty($artworkSlug)) {
                $artworkPositionService->updateArtwork($id, $artworkSlug);
            }
            if (!empty($newPosition)) {
                $artworkPositionService->updatePosition($gallerySlug, $id, $newPosition);
            }

            return null;
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}