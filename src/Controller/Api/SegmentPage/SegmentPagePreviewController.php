<?php

namespace Jinya\Controller\Api\SegmentPage;

use Jinya\Framework\BaseApiController;
use Jinya\Services\SegmentPages\SegmentPageServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SegmentPagePreviewController extends BaseApiController
{
    /**
     * @Route("/api/segment_page/{slug}/preview", methods={"GET"}, name="api_segment_page_preview")
     * @IsGranted("ROLE_WRITER")
     */
    public function getAction(
        string $slug,
        SegmentPageServiceInterface $segmentPageService
    ): Response {
        $page = $segmentPageService->get($slug);

        return $this->render('@Theme/SegmentPage/detail.html.twig', [
            'page' => $page,
        ]);
    }
}
