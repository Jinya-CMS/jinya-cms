<?php

namespace Jinya\Controller;

use Jinya\Components\Form\FormGeneratorInterface;
use Jinya\Framework\BaseController;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Mailing\MailerServiceInterface;
use Jinya\Services\Media\GalleryServiceInterface;
use Jinya\Services\Pages\PageServiceInterface;
use Jinya\Services\Routing\RouteServiceInterface;
use Jinya\Services\SegmentPages\SegmentPageServiceInterface;
use Jinya\Services\Users\UserServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class FrontendController extends BaseController
{
    /**
     * @Route("/{route}", name="frontend_default_index", requirements={"route": "^(?!api\\/|designer\\/).*"})
     *
     * @param string $route
     * @param RouteServiceInterface $routeService
     * @param LoggerInterface $logger
     * @return Response
     * @throws Throwable
     */
    public function indexAction(string $route, RouteServiceInterface $routeService, LoggerInterface $logger): Response
    {
        try {
            $routeEntry = $routeService->findByUrl('/' . $route);

            return $this->forwardToRoute($routeEntry);
        } catch (Throwable $throwable) {
            if (empty($route)) {
                return $this->render('@Theme/Default/index.html.twig');
            }

            $logger->error("Failed to load route $route");
            $logger->error($throwable->getMessage());
            $logger->error($throwable->getTraceAsString());

            throw $throwable;
        }
    }

    /**
     * @Route("/gallery/media/{slug}", name="frontend_media_gallery_details")
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function galleryDetailAction(string $slug, GalleryServiceInterface $galleryService): Response
    {
        $gallery = $galleryService->getBySlug($slug);

        return $this->render('@Theme/MediaGallery/detail.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    /**
     * @Route("/form/{slug}", name="frontend_form_details")
     *
     * @param string $slug
     * @param Request $request
     * @param FormServiceInterface $formService
     * @param FormGeneratorInterface $formGenerator
     * @param MailerServiceInterface $mailerService
     * @return Response
     */
    public function formDetailAction(
        string $slug,
        Request $request,
        FormServiceInterface $formService,
        FormGeneratorInterface $formGenerator,
        MailerServiceInterface $mailerService
    ): Response {
        $formEntity = $formService->get($slug);

        $form = $formGenerator->generateForm($formEntity);

        $form->handleRequest($request);

        $viewData = [
            'formEntity' => $formEntity,
            'form' => $form->createView(),
        ];

        if ($form->isSubmitted() && $form->isValid() && empty($request->get('contact_via_fax'))) {
            try {
                $failedRecipients = $mailerService->sendMail($formEntity, $form->getData());
                $viewData['mail_sent'] = empty($failedRecipients);
            } catch (Throwable $error) {
                $viewData['error'] = $error;
            }
        }

        return $this->render('@Theme/Form/detail.html.twig', $viewData);
    }

    /**
     * @Route("/page/{slug}", name="frontend_page_details")
     *
     * @param string $slug
     * @param PageServiceInterface $pageService
     * @return Response
     */
    public function pageDetailAction(string $slug, PageServiceInterface $pageService): Response
    {
        $page = $pageService->get($slug);

        return $this->render('@Theme/Page/detail.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/segment_page/{slug}", name="frontend_segment_page_details")
     *
     * @param string $slug
     * @param SegmentPageServiceInterface $segmentPageService
     * @return Response
     */
    public function segmentPageDetailAction(string $slug, SegmentPageServiceInterface $segmentPageService): Response
    {
        $page = $segmentPageService->get($slug);

        return $this->render('@Theme/SegmentPage/detail.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/profile/{id}", name="frontend_profile_details")
     *
     * @param int $id
     * @param UserServiceInterface $userService
     * @return Response
     */
    public function profileDetailAction(int $id, UserServiceInterface $userService): Response
    {
        $user = $userService->get($id);

        return $this->render('@Theme/Profile/detail.html.twig', [
            'profile' => $user,
        ]);
    }
}
