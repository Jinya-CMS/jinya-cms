<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 17:39
 */

namespace Jinya\Controller\Api\Media;

use Exception;
use Jinya\Entity\Media\Gallery;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Media\GalleryFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\GalleryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends BaseApiController
{
    /**
     * @Route("/api/media/gallery/:slug/random", methods={"GET"}, name="api_gallery_random")
     *
     * @param string $gallerySLug
     * @param GalleryServiceInterface $galleryService
     * @return Response
     * @throws Exception
     */
    public function getRandomAction(
        string $gallerySLug,
        GalleryServiceInterface $galleryService
    ): Response {
        $files = $galleryService->getBySlug($gallerySLug)->getFiles();
        return $files->get(random_int(0, $files->count()))->getPath();
    }

    /**
     * @Route("/api/media/gallery", methods={"GET"}, name="api_gallery_get_all")
     *
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAllAction(
        Request $request,
        GalleryServiceInterface $galleryService,
        GalleryFormatterInterface $galleryFormatter
    ): Response {
        [$data, $statusCode] = $this->tryExecute(function () use (
            $request,
            $galleryFormatter,
            $galleryService
        ) {
            $keyword = $request->get('keyword', '');
            $tag = $request->get('tag', '');

            $entityCount = $galleryService->countAll($keyword);
            $entities = array_map(static function ($gallery) use ($galleryFormatter) {
                return $galleryFormatter
                    ->init($gallery)
                    ->name()
                    ->orientation()
                    ->type()
                    ->slug()
                    ->description()
                    ->id()
                    ->format();
            }, $galleryService->getAll($keyword, $tag));

            return ['items' => $entities, 'count' => $entityCount];
        });

        return $this->json($data, $statusCode);
    }

    /**
     * @Route("/api/media/gallery/{slug}", methods={"GET"}, name="api_gallery_get")
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function getAction(
        string $slug,
        GalleryServiceInterface $galleryService,
        GalleryFormatterInterface $galleryFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($galleryFormatter, $slug, $galleryService) {
            $gallery = $galleryService->get($slug);
            $result = $galleryFormatter
                ->init($gallery)
                ->name()
                ->orientation()
                ->type()
                ->slug()
                ->description()
                ->files()
                ->id();

            if ($this->isGranted('ROLE_WRITER')) {
                $result = $result
                    ->updated()
                    ->created()
                    ->history();
            }

            return $result->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/gallery", methods={"POST"}, name="api_gallery_post")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function postAction(
        GalleryServiceInterface $galleryService,
        GalleryFormatterInterface $galleryFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($galleryService, $galleryFormatter) {
            $name = $this->getValue('name');
            $description = $this->getValue('description', '');
            $orientation = $this->getValue('orientation', 'horizontal');
            $type = $this->getValue('type', 'sequence');

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.gallery.field.name.missing']);
            }

            $gallery = new Gallery();
            $gallery->setName($name);
            $gallery->setDescription($description);
            $gallery->setOrientation($orientation);
            $gallery->setType($type);

            $galleryService->saveOrUpdate($gallery);

            return $galleryFormatter
                ->init($gallery)
                ->name()
                ->orientation()
                ->type()
                ->slug()
                ->description()
                ->id()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/gallery/{slug}", methods={"PUT"}, name="api_gallery_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @param GalleryFormatterInterface $galleryFormatter
     * @return Response
     */
    public function putAction(
        string $slug,
        GalleryServiceInterface $galleryService,
        GalleryFormatterInterface $galleryFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($slug, $galleryService, $galleryFormatter) {
            $gallery = $galleryService->get($slug);

            $name = $this->getValue('name', $gallery->getName());
            $description = $this->getValue('description', $gallery->getDescription());
            $orientation = $this->getValue('orientation', $gallery->getOrientation());
            $type = $this->getValue('masonry', $gallery->getType());
            $slug = $this->getValue('slug', $gallery->getSlug());

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.gallery.field.name.missing']);
            }

            $gallery = new Gallery();
            $gallery->setName($name);
            $gallery->setDescription($description);
            $gallery->setOrientation($orientation);
            $gallery->setType($type);
            $gallery->setSlug($slug);
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/gallery/{slug}", methods={"DELETE"}, name="api_gallery_delete")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function deleteAction(
        string $slug,
        GalleryServiceInterface $galleryService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($slug, $galleryService) {
            $gallery = $galleryService->get($slug);

            $galleryService->delete($gallery);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
