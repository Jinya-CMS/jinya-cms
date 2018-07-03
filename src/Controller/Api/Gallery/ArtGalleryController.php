<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 17:39
 */

namespace Jinya\Controller\Api\Gallery;

use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Gallery\ArtGalleryFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtGalleryController extends BaseApiController
{
    /**
     * @Route("/api/gallery/art", methods={"GET"}, name="api_gallery_art_get_all")
     *
     * @param ArtGalleryServiceInterface $galleryService
     * @param ArtGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAllAction(ArtGalleryServiceInterface $galleryService, ArtGalleryFormatterInterface $galleryFormatter): Response
    {
        return $this->getAllLabeled($galleryService, function ($gallery) use ($galleryFormatter) {
            return $galleryFormatter
                ->init($gallery)
                ->name()
                ->background()
                ->orientation()
                ->slug()
                ->description()
                ->format();
        });
    }

    /**
     * @Route("/api/gallery/art/{slug}", methods={"GET"}, name="api_gallery_art_get")
     *
     * @param string $slug
     * @param ArtGalleryServiceInterface $galleryService
     * @param ArtGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAction(string $slug, ArtGalleryServiceInterface $galleryService, ArtGalleryFormatterInterface $galleryFormatter): Response
    {
        /* @noinspection PhpParamsInspection */
        return $this->getArt($slug, $galleryService, function ($gallery) use ($galleryFormatter) {
            $result = $galleryFormatter->init($gallery)
                ->name()
                ->slug()
                ->background()
                ->description()
                ->orientation();

            if ($this->isGranted('ROLE_WRITER')) {
                $result = $result->updated()
                    ->id()
                    ->created()
                    ->labels()
                    ->artworks();
            }

            return $result->format();
        });
    }

    /**
     * @Route("/api/gallery/art", methods={"POST"}, name="api_gallery_art_post")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param Request $request
     * @param ArtGalleryServiceInterface $galleryService
     * @param ArtGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function postAction(Request $request, ArtGalleryServiceInterface $galleryService, ArtGalleryFormatterInterface $galleryFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $galleryFormatter) {
            $name = $this->getValue('name');
            $description = $this->getValue('description', '');
            $orientation = $this->getValue('orientation', 'horizontal');
            $slug = $this->getValue('slug', '');

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.gallery.field.name.missing']);
            }

            $gallery = new ArtGallery();
            $gallery->setName($name);
            $gallery->setSlug($slug);
            $gallery->setDescription($description);
            $gallery->setOrientation($orientation);

            return $galleryFormatter->init($galleryService->saveOrUpdate($gallery))
                ->name()
                ->slug()
                ->description()
                ->orientation()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/art/{slug}", methods={"PUT"}, name="api_gallery_art_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param Request $request
     * @param ArtGalleryServiceInterface $galleryService
     * @param ArtGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function putAction(string $slug, Request $request, ArtGalleryServiceInterface $galleryService, ArtGalleryFormatterInterface $galleryFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $request, $galleryService, $galleryFormatter) {
            $gallery = $galleryService->get($slug);

            $name = $this->getValue('name', $gallery->getName());
            $description = $this->getValue('description', $gallery->getDescription());
            $orientation = $this->getValue('orientation', $gallery->getOrientation());
            $slug = $this->getValue('slug', $gallery->getSlug());

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.gallery.field.name.missing']);
            }

            $gallery->setName($name);
            $gallery->setSlug($slug);
            $gallery->setDescription($description);
            $gallery->setOrientation($orientation);

            return $galleryFormatter->init($galleryService->saveOrUpdate($gallery))
                ->name()
                ->slug()
                ->description()
                ->orientation()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/art/{slug}", methods={"DELETE"}, name="api_gallery_art_delete")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param string $slug
     * @param ArtGalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteAction(string $slug, ArtGalleryServiceInterface $galleryService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $galleryService, $mediaService) {
            $gallery = $galleryService->get($slug);
            if (!empty($gallery->getBackground())) {
                $mediaService->deleteMedia($gallery->getBackground());
            }

            $galleryService->delete($gallery);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
