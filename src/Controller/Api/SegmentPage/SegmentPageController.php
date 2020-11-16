<?php

namespace Jinya\Controller\Api\SegmentPage;

use Jinya\Entity\SegmentPage\SegmentPage;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\SegmentPage\SegmentPageFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\SegmentPages\SegmentPageServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SegmentPageController extends BaseApiController
{
    /**
     * @Route("/api/segment_page", methods={"GET"}, name="api_segment_page_get_all")
     */
    public function getAllAction(
        Request $request,
        SegmentPageServiceInterface $segmentPageService,
        SegmentPageFormatterInterface $segmentPageFormatter
    ): Response {
        [$data, $statusCode] = $this->tryExecute(function () use (
            $request,
            $segmentPageService,
            $segmentPageFormatter
        ) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');

            $entityCount = $segmentPageService->countAll($keyword);
            $entities = array_map(function (SegmentPage $segmentPage) use ($segmentPageFormatter) {
                $result = $segmentPageFormatter
                    ->init($segmentPage)
                    ->name()
                    ->slug()
                    ->segments();

                if ($this->isGranted('ROLE_WRITER')) {
                    $result = $result
                        ->updated()
                        ->created();
                }

                return $result->format();
            }, $segmentPageService->getAll($keyword));

            $parameter = ['offset' => $offset, 'count' => $count, 'keyword' => $keyword];

            return $this->formatListResult(
                $entityCount,
                $offset,
                $count,
                $parameter,
                'api_segment_page_get_all',
                $entities
            );
        });

        return $this->json($data, $statusCode);
    }

    /**
     * @Route("/api/segment_page/{slug}", methods={"GET"}, name="api_get_segment_page")
     */
    public function getAction(
        string $slug,
        SegmentPageFormatterInterface $segmentPageFormatter,
        SegmentPageServiceInterface $segmentPageService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use (
            $segmentPageFormatter,
            $slug,
            $segmentPageService
        ) {
            $segmentPage = $segmentPageService->get($slug);

            $segmentPageFormatter
                ->init($segmentPage)
                ->name()
                ->slug()
                ->segments();

            if ($this->isGranted('ROLE_WRITER')) {
                $segmentPageFormatter
                    ->history()
                    ->updated()
                    ->created();
            }

            return $segmentPageFormatter->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/segment_page", methods={"POST"}, name="api_segment_page_post")
     * @IsGranted("ROLE_WRITER")
     */
    public function postAction(
        SegmentPageServiceInterface $segmentPageService,
        SegmentPageFormatterInterface $segmentPageFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($segmentPageService, $segmentPageFormatter) {
            $slug = $this->getValue('slug', '');
            $name = $this->getValue('name', '');

            $missingFields = [];
            if (empty($name)) {
                $missingFields['name'] = 'api.segment_page.field.name.missing';
            }

            if (!empty($missingFields)) {
                throw new MissingFieldsException($missingFields);
            }

            $page = new SegmentPage();
            $page->setSlug($slug);
            $page->setName($name);

            $segmentPageService->saveOrUpdate($page);

            return $segmentPageFormatter
                ->init($page)
                ->name()
                ->slug()
                ->created()
                ->segments()
                ->updated()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/segment_page/{slug}", methods={"PUT"}, name="api_segment_page_put")
     * @IsGranted("ROLE_WRITER")
     */
    public function putAction(string $slug, SegmentPageServiceInterface $segmentPageService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($slug, $segmentPageService) {
            $segmentPage = $segmentPageService->get($slug);
            $slug = $this->getValue('slug', $segmentPage->getSlug());
            $name = $this->getValue('name', $segmentPage->getName());

            $segmentPage->setSlug($slug);
            $segmentPage->setName($name);

            $segmentPageService->saveOrUpdate($segmentPage);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/segment_page/{slug}", methods={"DELETE"}, name="api_segment_page_delete")
     * @IsGranted("ROLE_WRITER")
     */
    public function deleteAction(string $slug, SegmentPageServiceInterface $segmentPageService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($slug, $segmentPageService) {
            $segmentPageService->delete($segmentPageService->get($slug));
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
