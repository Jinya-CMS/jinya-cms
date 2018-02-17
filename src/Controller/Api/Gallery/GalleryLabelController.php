<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 21:42
 */

namespace Jinya\Controller\Api\Gallery;


use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\GalleryServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryLabelController extends BaseApiController
{
    /**
     * @Route("/api/gallery/{slug}/label", methods={"GET"}, name="api_gallery_label_get")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function getAction(string $slug, GalleryServiceInterface $galleryService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $galleryService) {
            $gallery = $galleryService->get($slug);

            return $gallery->getLabels()->toArray();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}/label/{name}", methods={"PUT"}, name="api_gallery_label_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param string $name
     * @param GalleryServiceInterface $galleryService
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function putAction(string $slug, string $name, GalleryServiceInterface $galleryService, LabelServiceInterface $labelService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $name, $galleryService, $labelService) {
            $gallery = $galleryService->get($slug);

            $gallery->getLabels()->add($labelService->getLabel($name));

            return $galleryService->saveOrUpdate($gallery);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}/label", methods={"PUT"}, name="api_gallery_label_put_batch")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function putBatchAction(string $slug, GalleryServiceInterface $galleryService, LabelServiceInterface $labelService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $galleryService, $labelService) {
            $gallery = $galleryService->get($slug);
            $labels = $this->getValue('labels', []);

            foreach ($labels as $label) {
                $labelEntity = $labelService->getLabel($label);

                if (!$gallery->getLabels()->contains($labelEntity)) {
                    $gallery->getLabels()->add($labelEntity);
                }
            }

            $galleryService->saveOrUpdate($gallery);

            return null;
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}/label/{name}", methods={"DELETE"}, name="api_gallery_label_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param string $name
     * @param GalleryServiceInterface $galleryService
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function deleteAction(string $slug, string $name, GalleryServiceInterface $galleryService, LabelServiceInterface $labelService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $name, $galleryService, $labelService) {
            $gallery = $galleryService->get($slug);

            $gallery->getLabels()->removeElement($labelService->getLabel($name));
            $galleryService->saveOrUpdate($gallery);

            return null;
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}/label", methods={"DELETE"}, name="api_gallery_label_delete_batch")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function deleteBatchAction(string $slug, GalleryServiceInterface $galleryService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $galleryService) {
            $gallery = $galleryService->get($slug);

            $gallery->getLabels()->clear();
            $galleryService->saveOrUpdate($gallery);

            return null;
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}