<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 17:39
 */

namespace Jinya\Controller\Api\Gallery;

use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Gallery\VideoGalleryFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\VideoGalleryServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoGalleryController extends BaseApiController
{
    /**
     * @Route("/api/gallery/video", methods={"GET"}, name="api_gallery_video_get_all")
     *
     * @param Request $request
     * @param VideoGalleryServiceInterface $galleryService
     * @param VideoGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAllAction(Request $request, VideoGalleryServiceInterface $galleryService, VideoGalleryFormatterInterface $galleryFormatter): Response
    {
        list($data, $statusCode) = $this->tryExecute(function () use ($request, $galleryService, $galleryFormatter) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');

            $entityCount = $galleryService->countAll($keyword);
            $entities = array_map(function ($gallery) use ($galleryFormatter) {
                return $galleryFormatter
                    ->init($gallery)
                    ->name()
                    ->background()
                    ->backgroundDimensions()
                    ->orientation()
                    ->masonry()
                    ->slug()
                    ->description()
                    ->format();
            }, $galleryService->getAll($offset, $count, $keyword));

            $parameter = ['offset' => $offset, 'count' => $count, 'keyword' => $keyword];

            return $this->formatListResult($entityCount, $offset, $count, $parameter, 'api_gallery_video_get_all', $entities);
        });

        return $this->json($data, $statusCode);
    }

    /**
     * @Route("/api/gallery/video/{slug}", methods={"GET"}, name="api_gallery_video_get")
     *
     * @param string $slug
     * @param VideoGalleryServiceInterface $galleryService
     * @param VideoGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAction(string $slug, VideoGalleryServiceInterface $galleryService, VideoGalleryFormatterInterface $galleryFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $galleryService, $galleryFormatter) {
            $gallery = $galleryService->get($slug);

            $galleryFormatter->init($gallery)
                ->name()
                ->slug()
                ->background()
                ->backgroundDimensions()
                ->description()
                ->masonry()
                ->videos()
                ->orientation();

            if ($this->isGranted('ROLE_WRITER')) {
                $galleryFormatter->updated()
                    ->id()
                    ->created();
            }

            return [
                'success' => true,
                'item' => $galleryFormatter->format(),
            ];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/video", methods={"POST"}, name="api_gallery_video_post")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param Request $request
     * @param VideoGalleryServiceInterface $galleryService
     * @param VideoGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function postAction(Request $request, VideoGalleryServiceInterface $galleryService, VideoGalleryFormatterInterface $galleryFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $galleryFormatter) {
            $name = $this->getValue('name');
            $description = $this->getValue('description', '');
            $orientation = $this->getValue('orientation', 'horizontal');
            $slug = $this->getValue('slug', '');

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.gallery.field.name.missing']);
            }

            $gallery = new VideoGallery();
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
     * @Route("/api/gallery/video/{slug}", methods={"PUT"}, name="api_gallery_video_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param Request $request
     * @param VideoGalleryServiceInterface $galleryService
     * @param VideoGalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function putAction(string $slug, Request $request, VideoGalleryServiceInterface $galleryService, VideoGalleryFormatterInterface $galleryFormatter): Response
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
     * @Route("/api/gallery/video/{slug}", methods={"DELETE"}, name="api_gallery_video_delete")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param string $slug
     * @param VideoGalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteAction(string $slug, VideoGalleryServiceInterface $galleryService, MediaServiceInterface $mediaService): Response
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
