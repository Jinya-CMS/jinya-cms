<?php

namespace Jinya\Controller\Api\SegmentPage;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\SegmentPage\SegmentFormatterInterface;
use Jinya\Formatter\SegmentPage\SegmentPageFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\SegmentPages\SegmentPageServiceInterface;
use Jinya\Services\SegmentPages\SegmentServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SegmentController extends BaseApiController
{
    /**
     * @Route("/api/segment_page/{slug}/segment", methods={"GET"}, name="api_segment_page_segment_get")
     *
     * @param string $slug
     * @param SegmentPageServiceInterface $segmentPageService
     * @param SegmentPageFormatterInterface $segmentPageFormatter
     * @return Response
     */
    public function getAction(
        string $slug,
        SegmentPageServiceInterface $segmentPageService,
        SegmentPageFormatterInterface $segmentPageFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use (
            $slug,
            $segmentPageService,
            $segmentPageFormatter
        ) {
            $segmentPage = $segmentPageService->get($slug);

            return $segmentPageFormatter->init($segmentPage)->segments()->format()['segments'];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/segment_page/{slug}/segment", methods={"POST"}, name="api_segment_page_segment_post")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param SegmentServiceInterface $segmentService
     * @param SegmentFormatterInterface $segmentFormatter
     * @return Response
     */
    public function postAction(
        string $slug,
        SegmentServiceInterface $segmentService,
        SegmentFormatterInterface $segmentFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($slug, $segmentService, $segmentFormatter) {
            $position = $this->getValue('position', 0);
            $gallerySlug = $this->getValue('gallery');
            $fileId = $this->getValue('file');
            $formSlug = $this->getValue('form');
            $html = $this->getValue('html');
            $action = $this->getValue('action', Segment::ACTION_NONE);
            $target = $this->getValue('target', '');
            $script = $this->getValue('script', '');

            if (null !== $html) {
                $segment = $segmentService->saveHtmlSegment($html, $slug, $position, $action, $target, $script);
            }

            if (null !== $formSlug) {
                $segment = $segmentService->saveFormSegment($formSlug, $slug, $position, $action, $target, $script);
            }

            if (null !== $gallerySlug) {
                $segment = $segmentService->saveGallerySegment(
                    $gallerySlug,
                    $slug,
                    $position,
                    $action,
                    $target,
                    $script
                );
            }

            if (null !== $fileId) {
                $segment = $segmentService->saveFileSegment($fileId, $slug, $position, $action, $target, $script);
            }

            if (!isset($segment)) {
                throw new MissingFieldsException(['type' => 'api.segment_page.segment.field.type.missing']);
            }

            return $segmentFormatter
                ->init($segment)
                ->id()
                ->position()
                ->form()
                ->html()
                ->script()
                ->target()
                ->action()
                ->gallery()
                ->file()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/segment_page/{slug}/segment/{id}", methods={"DELETE"}, name="api_segment_page_segment_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int id$
     * @param SegmentServiceInterface $segmentService
     * @return Response
     */
    public function deleteAction(int $id, SegmentServiceInterface $segmentService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $segmentService) {
            $segmentService->deleteSegment($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/segment_page/{slug}/segment/{id}", methods={"PUT"}, name="api_segment_page_segment_put")
     * @Route("/api/segment_page/{slug}/segment/{id}/{oldPosition}", methods={"PUT"},
     *     name="api_segment_page_segment_put_with_position")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param int $oldPosition
     * @param string $slug
     * @param SegmentServiceInterface $segmentService
     * @return Response
     */
    public function putPositionAction(
        int $id,
        string $slug,
        SegmentServiceInterface $segmentService,
        int $oldPosition = -1
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($oldPosition, $id, $slug, $segmentService) {
            $position = $this->getValue('position', -1);
            $segment = $segmentService->get($id);

            $html = $this->getValue('html');
            $gallerySlug = $this->getValue('gallery');
            $fileId = $this->getValue('file');
            $action = $this->getValue('action', $segment->getAction());
            $target = $this->getValue('target', $segment->getTarget());
            $script = $this->getValue('script', $segment->getScript());

            $segmentService->updateAction($id, $action, $target, $script);

            if (-1 !== $position) {
                $segmentService->updatePosition($slug, $id, $oldPosition, $position);
            }

            if (null !== $gallerySlug) {
                $segmentService->updateGallerySegment($gallerySlug, $id);
            }

            if (null !== $fileId) {
                $segmentService->updateFileSegment($fileId, $id, $script, $action, $target);
            }

            if (null !== $html) {
                $segmentService->updateHtmlSegment($html, $id);
            }
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }
}
