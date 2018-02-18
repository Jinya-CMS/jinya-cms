<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 23:10
 */

namespace Jinya\Controller\Api\Artwork;


use Jinya\Entity\Artwork;
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
use Symfony\Component\Routing\RouterInterface;

class ArtworkController extends BaseApiController
{
    /**
     * @Route("/api/artwork", methods={"GET"}, name="api_artwork_get_all")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param Request $request
     * @param ArtworkServiceInterface $artworkService
     * @param RouterInterface $router
     * @param LabelServiceInterface $labelService
     * @param ArtworkFormatterInterface $artworkFormatter
     * @return Response
     */
    public function getAllAction(Request $request, ArtworkServiceInterface $artworkService, RouterInterface $router, LabelServiceInterface $labelService, ArtworkFormatterInterface $artworkFormatter): Response
    {
        return $this->getAllArt($request, $artworkService, $router, $labelService, function (array $artworks) use ($artworkFormatter) {
            $data = [];

            foreach ($artworks as $gallery) {
                $data[] = $artworkFormatter
                    ->init($gallery)
                    ->name()
                    ->picture()
                    ->slug()
                    ->description()
                    ->format();
            }

            return $data;
        });
    }

    /**
     * @Route("/api/artwork/{slug}", methods={"GET"}, name="api_artwork_get")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param string $slug
     * @param ArtworkServiceInterface $artworkService
     * @param ArtworkFormatterInterface $artworkFormatter
     * @return Response
     */
    public function getAction(string $slug, ArtworkServiceInterface $artworkService, ArtworkFormatterInterface $artworkFormatter): Response
    {
        return $this->getArt($slug, $artworkService, function ($artwork) use ($artworkFormatter) {
            $result = $artworkFormatter->init($artwork)
                ->name()
                ->slug()
                ->picture()
                ->description();

            if ($this->isGranted('ROLE_WRITER')) {
                $result = $result->updated()
                    ->created()
                    ->labels()
                    ->galleries();
            }

            return $result;
        });
    }

    /**
     * @Route("/api/artwork", methods={"POST"}, name="api_artwork_post")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param ArtworkServiceInterface $artworkService
     * @param ArtworkFormatterInterface $artworkFormatter
     * @return Response
     */
    public function postAction(Request $request, ArtworkServiceInterface $artworkService, ArtworkFormatterInterface $artworkFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $artworkService, $artworkFormatter) {
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
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param Request $request
     * @param ArtworkServiceInterface $artworkService
     * @param ArtworkFormatterInterface $artworkFormatter
     * @return Response
     */
    public function putAction(string $slug, Request $request, ArtworkServiceInterface $artworkService, ArtworkFormatterInterface $artworkFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $request, $artworkService, $artworkFormatter) {
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
     * @IsGranted("ROLE_ADMIN")
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
            if (!empty($artwork->getPicture())) {
                $mediaService->deleteMedia($artwork->getPicture());
            }

            $artworkService->delete($artwork);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}