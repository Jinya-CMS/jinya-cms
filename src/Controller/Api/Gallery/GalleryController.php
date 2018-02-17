<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 17:39
 */

namespace Jinya\Controller\Api\Gallery;

use Jinya\Entity\Gallery;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\GalleryServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class GalleryController extends BaseApiController
{
    /**
     * @Route("/api/gallery", methods={"GET"}, name="api_gallery_get_all")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param GalleryServiceInterface $galleryService
     * @param Request $request
     * @param RouterInterface $router
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function getAllAction(Request $request, GalleryServiceInterface $galleryService, RouterInterface $router, LabelServiceInterface $labelService): Response
    {
        return $this->getAllArt($request, $galleryService, $router, $labelService);
    }

    /**
     * @Route("/api/gallery/{slug}", methods={"GET"}, name="api_gallery_get")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function getAction(string $slug, GalleryServiceInterface $galleryService): Response
    {
        return $this->getArt($slug, $galleryService);
    }

    /**
     * @Route("/api/gallery", methods={"POST"}, name="api_gallery_post")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function postAction(Request $request, GalleryServiceInterface $galleryService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService) {
            $name = $this->getValue('name');
            $description = $this->getValue('description', '');
            $orientation = $this->getValue('orientation', 'horizontal');
            $slug = $this->getValue('slug', null);
            $labels = $this->getValue('labels', []);

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.gallery.field.name.missing']);
            }

            $gallery = new Gallery();
            $gallery->setName($name);
            $gallery->setSlug($slug);
            $gallery->setDescription($description);
            $gallery->setOrientation($orientation);

            $gallery = $galleryService->saveOrUpdate($gallery);

            return $galleryService->setLabels($gallery, $labels);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}", methods={"PUT"}, name="api_gallery_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function putAction(string $slug, Request $request, GalleryServiceInterface $galleryService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $request, $galleryService) {
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

            $gallery = $galleryService->saveOrUpdate($gallery);

            return $gallery;
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}", methods={"DELETE"}, name="api_gallery_delete")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteAction(string $slug, GalleryServiceInterface $galleryService, MediaServiceInterface $mediaService): Response
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