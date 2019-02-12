<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 23:10
 */

namespace Jinya\Controller\Api\Artwork;

use Jinya\Entity\Artwork\Artwork;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Artwork\ArtworkFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtworkController extends BaseApiController
{
    /**
     * @Route("/api/artwork", methods={"GET"}, name="api_artwork_get_all")
     *
     * @param Request $request
     * @param LabelServiceInterface $labelService
     * @param ArtworkServiceInterface $artworkService
     * @param ArtworkFormatterInterface $artworkFormatter
     * @return Response
     */
    public function getAllAction(Request $request, LabelServiceInterface $labelService, ArtworkServiceInterface $artworkService, ArtworkFormatterInterface $artworkFormatter): Response
    {
        list($data, $statusCode) = $this->tryExecute(function () use ($labelService, $request, $artworkFormatter, $artworkService) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');
            $label = $request->get('label', null);

            if ($label) {
                $label = $labelService->getLabel($label);
            }

            $entityCount = $artworkService->countAll($keyword);
            $entities = array_map(function ($artwork) use ($artworkFormatter) {
                return $artworkFormatter
                    ->init($artwork)
                    ->name()
                    ->picture()
                    ->slug()
                    ->description()
                    ->dimensions()
                    ->format();
            }, $artworkService->getAll($offset, $count, $keyword, $label));

            $parameter = ['offset' => $offset, 'count' => $count, 'keyword' => $keyword];

            return $this->formatListResult($entityCount, $offset, $count, $parameter, 'api_artwork_get_all', $entities);
        });

        return $this->json($data, $statusCode);
    }

    /**
     * @Route("/api/artwork/{slug}", methods={"GET"}, name="api_artwork_get")
     *
     * @param string $slug
     * @param ArtworkServiceInterface $artworkService
     * @param ArtworkFormatterInterface $artworkFormatter
     * @return Response
     */
    public function getAction(string $slug, ArtworkServiceInterface $artworkService, ArtworkFormatterInterface $artworkFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($artworkFormatter, $slug, $artworkService) {
            $artwork = $artworkService->get($slug);
            $result = $artworkFormatter->init($artwork)
                ->name()
                ->slug()
                ->picture()
                ->dimensions()
                ->description();

            if ($this->isGranted('ROLE_WRITER')) {
                $result = $result->updated()
                    ->id()
                    ->created()
                    ->labels()
                    ->galleries();
            }

            return [
                'success' => true,
                'item' => $result->format(),
            ];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/artwork", methods={"POST"}, name="api_artwork_post")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param ArtworkServiceInterface $artworkService
     * @param ArtworkFormatterInterface $artworkFormatter
     * @return Response
     */
    public function postAction(ArtworkServiceInterface $artworkService, ArtworkFormatterInterface $artworkFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($artworkService, $artworkFormatter) {
            $name = $this->getValue('name');
            $description = $this->getValue('description', '');
            $slug = $this->getValue('slug', '');

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.artwork.field.name.missing']);
            }

            $artwork = new Artwork();
            $artwork->setName($name);
            $artwork->setSlug($slug);
            $artwork->setDescription($description);
            $artwork->setPicture('');

            return $artworkFormatter
                ->init($artworkService->saveOrUpdate($artwork))
                ->name()
                ->slug()
                ->description()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/artwork/{slug}", methods={"PUT"}, name="api_artwork_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param ArtworkServiceInterface $artworkService
     * @param ArtworkFormatterInterface $artworkFormatter
     * @return Response
     */
    public function putAction(string $slug, ArtworkServiceInterface $artworkService, ArtworkFormatterInterface $artworkFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $artworkService, $artworkFormatter) {
            $artwork = $artworkService->get($slug);

            $name = $this->getValue('name', $artwork->getName());
            $description = $this->getValue('description', $artwork->getDescription());
            $slug = $this->getValue('slug', $artwork->getSlug());

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.artwork.field.name.missing']);
            }

            $artwork->setName($name);
            $artwork->setSlug($slug);
            $artwork->setDescription($description);

            return $artworkFormatter
                ->init($artworkService->saveOrUpdate($artwork))
                ->name()
                ->slug()
                ->description()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/artwork/{slug}", methods={"DELETE"}, name="api_artwork_delete")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param string $slug
     * @param ArtworkServiceInterface $artworkService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteAction(string $slug, ArtworkServiceInterface $artworkService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $artworkService, $mediaService) {
            $artwork = $artworkService->get($slug);
            $picture = $artwork->getPicture();
            $artworkService->delete($artwork);

            if (!empty($artwork->getPicture())) {
                $mediaService->deleteMedia($picture);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
