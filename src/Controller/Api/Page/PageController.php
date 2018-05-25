<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 22:45.
 */

namespace Jinya\Controller\Api\Page;

use Jinya\Entity\Page;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Page\PageFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Pages\PageServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends BaseApiController
{
    /**
     * @Route("/api/page", methods={"GET"}, name="api_page_get_all")
     *
     * @param PageServiceInterface   $pageService
     * @param PageFormatterInterface $pageFormatter
     *
     * @return Response
     */
    public function getAllAction(PageServiceInterface $pageService, PageFormatterInterface $pageFormatter): Response
    {
        return $this->getAllStaticContent($pageService, function ($item) use ($pageFormatter) {
            return $pageFormatter
                ->init($item)
                ->title()
                ->slug()
                ->format();
        });
    }

    /**
     * @Route("/api/page/{slug}", methods={"GET"}, name="api_page_get")
     *
     * @param string                 $slug
     * @param PageServiceInterface   $pageService
     * @param PageFormatterInterface $pageFormatter
     *
     * @return Response
     */
    public function getAction(string $slug, PageServiceInterface $pageService, PageFormatterInterface $pageFormatter): Response
    {
        return $this->getStaticContent($pageService, $slug, function ($item) use ($pageFormatter) {
            $pageFormatter
                ->init($item)
                ->slug()
                ->name()
                ->title()
                ->id()
                ->content();

            if ($this->isGranted('ROLE_WRITER')) {
                $pageFormatter
                    ->history()
                    ->updated()
                    ->created();
            }

            return $pageFormatter->format();
        });
    }

    /**
     * @Route("/api/page", methods={"POST"}, name="api_page_post")
     * @IsGranted("ROLE_WRITER")
     *
     * @param PageServiceInterface   $pageService
     * @param PageFormatterInterface $pageFormatter
     *
     * @return Response
     */
    public function postAction(PageServiceInterface $pageService, PageFormatterInterface $pageFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($pageService, $pageFormatter) {
            $title = $this->getValue('title');
            $slug = $this->getValue('slug', '');
            $name = $this->getValue('name', $title);
            $content = $this->getValue('content');

            $missingFields = [];
            if (empty($title)) {
                $missingFields['title'] = 'api.page.field.title.missing';
            }
            if (empty($content)) {
                $missingFields['content'] = 'api.page.field.content.missing';
            }

            if (!empty($missingFields)) {
                throw new MissingFieldsException($missingFields);
            }

            $page = new Page();
            $page->setSlug($slug);
            $page->setTitle($title);
            $page->setName($name);
            $page->setContent($content);

            $pageService->saveOrUpdate($page);

            return $pageFormatter
                ->init($page)
                ->title()
                ->slug()
                ->content()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/page/{slug}", methods={"PUT"}, name="api_page_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string               $slug
     * @param PageServiceInterface $pageService
     *
     * @return Response
     */
    public function putAction(string $slug, PageServiceInterface $pageService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $pageService) {
            $page = $pageService->get($slug);
            $title = $this->getValue('title', $page->getTitle());
            $slug = $this->getValue('slug', $page->getSlug());
            $name = $this->getValue('name', $page->getName());
            $content = $this->getValue('content', $page->getContent());

            $page->setSlug($slug);
            $page->setTitle($title);
            $page->setName($name);
            $page->setContent($content);

            $pageService->saveOrUpdate($page);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/page/{slug}", methods={"DELETE"}, name="api_page_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string               $slug
     * @param PageServiceInterface $pageService
     *
     * @return Response
     */
    public function deleteAction(string $slug, PageServiceInterface $pageService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $pageService) {
            $pageService->delete($pageService->get($slug));
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
