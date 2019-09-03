<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 21:42
 */

namespace Jinya\Controller\Api\Artwork;

use Jinya\Formatter\Label\LabelFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtworkLabelController extends BaseApiController
{
    /**
     * @Route("/api/artwork/{slug}/label", methods={"GET"}, name="api_artwork_label_get")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param ArtworkServiceInterface $artworkService
     * @param LabelFormatterInterface $labelFormatter
     * @return Response
     */
    public function getAction(
        string $slug,
        ArtworkServiceInterface $artworkService,
        LabelFormatterInterface $labelFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($slug, $artworkService, $labelFormatter) {
            $artwork = $artworkService->get($slug);
            $labels = [];

            foreach ($artwork->getLabels() as $label) {
                $labels[] = $labelFormatter
                    ->init($label)
                    ->name()
                    ->format();
            }

            return $labels;
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/artwork/{slug}/label/{name}", methods={"PUT"}, name="api_artwork_label_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param string $name
     * @param ArtworkServiceInterface $artworkService
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function putAction(
        string $slug,
        string $name,
        ArtworkServiceInterface $artworkService,
        LabelServiceInterface $labelService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($slug, $name, $artworkService, $labelService) {
            $artwork = $artworkService->get($slug);

            $artwork->getLabels()->add($labelService->getLabel($name));

            $artworkService->saveOrUpdate($artwork);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/artwork/{slug}/label", methods={"PUT"}, name="api_artwork_label_put_batch")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param ArtworkServiceInterface $artworkService
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function putBatchAction(
        string $slug,
        ArtworkServiceInterface $artworkService,
        LabelServiceInterface $labelService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($slug, $artworkService, $labelService) {
            $artwork = $artworkService->get($slug);
            $labels = $this->getValue('labels', []);

            foreach ($labels as $label) {
                $labelEntity = $labelService->getLabel($label);

                if (!$artwork->getLabels()->contains($labelEntity)) {
                    $artwork->getLabels()->add($labelEntity);
                }
            }

            $artworkService->saveOrUpdate($artwork);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/artwork/{slug}/label/{name}", methods={"DELETE"}, name="api_artwork_label_delete")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param string $name
     * @param ArtworkServiceInterface $artworkService
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function deleteAction(
        string $slug,
        string $name,
        ArtworkServiceInterface $artworkService,
        LabelServiceInterface $labelService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($slug, $name, $artworkService, $labelService) {
            $artwork = $artworkService->get($slug);

            $artwork->getLabels()->removeElement($labelService->getLabel($name));
            $artworkService->saveOrUpdate($artwork);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/artwork/{slug}/label", methods={"DELETE"}, name="api_artwork_label_delete_batch")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param ArtworkServiceInterface $artworkService
     * @return Response
     */
    public function deleteBatchAction(string $slug, ArtworkServiceInterface $artworkService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($slug, $artworkService) {
            $artwork = $artworkService->get($slug);

            $artwork->getLabels()->clear();
            $artworkService->saveOrUpdate($artwork);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
