<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 23:18
 */

namespace Jinya\Controller\Api\Artwork;

use Jinya\Entity\Artwork;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArtworkPictureController extends BaseApiController
{
    /**
     * @Route("/api/artwork/{slug}/picture", methods={"GET"}, name="api_artwork_picture_get")
     *
     * @param string $slug
     * @param Request $request
     * @param ArtworkServiceInterface $artworkService
     * @param MediaServiceInterface $mediaService
     *
     * @return Response
     */
    public function getPictureAction(string $slug, Request $request, ArtworkServiceInterface $artworkService, MediaServiceInterface $mediaService): Response
    {
        /** @var $data Artwork|array */
        list($data, $status) = $this->tryExecute(function () use ($request, $artworkService, $slug) {
            $artwork = $artworkService->get($slug);
            if (empty($artwork->getPicture())) {
                throw new FileNotFoundException($artwork->getName());
            }

            return $artwork;
        });

        if (200 !== $status) {
            return $this->json($data, $status);
        } else {
            return $this->file($mediaService->getMedia($data->getPicture()), $data->getName().'.jpg');
        }
    }

    /**
     * @Route("/api/artwork/{slug}/picture", methods={"PUT"}, name="api_artwork_picture_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param Request $request
     * @param ArtworkServiceInterface $artworkService
     * @param MediaServiceInterface $mediaService
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return Response
     */
    public function putPictureAction(string $slug, Request $request, ArtworkServiceInterface $artworkService, MediaServiceInterface $mediaService, UrlGeneratorInterface $urlGenerator): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $artworkService, $mediaService, $urlGenerator, $slug) {
            $artwork = $artworkService->get($slug);

            $picture = $request->getContent(true);
            $picturePath = $mediaService->saveMedia($picture, MediaServiceInterface::CONTENT_IMAGE);

            if ($picture) {
                $artwork->setPicture($picturePath);
            }

            $artworkService->saveOrUpdate($artwork);

            return $urlGenerator->generate('api_artwork_picture_get', ['slug' => $artwork->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }
}
