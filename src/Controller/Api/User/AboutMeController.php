<?php

namespace Jinya\Controller\Api\User;

use Jinya\Services\Users\UserServiceInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutMeController extends BaseUserController
{
    /**
     * @Route("/api/user/{id}/about", methods={"GET"}, name="api_user_get_about")
     *
     * @param int $id
     * @param UserServiceInterface $userService
     * @return Response
     */
    public function getAction(int $id, UserServiceInterface $userService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $userService) {
            $user = $userService->get($id);

            return $user->getAboutMe();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/user/{id}/about", methods={"POST"}, name="api_user_post_about")
     *
     * @param int $id
     * @param Request $request
     * @param UserServiceInterface $userService
     * @return Response
     */
    public function postAction(int $id, Request $request, UserServiceInterface $userService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $request, $userService) {
            if ($this->isCurrentUser($id)) {
                $user = $userService->get($id);
                $about = $request->getContent();
                $user->setAboutMe($about);

                $userService->saveOrUpdate($user);
            } else {
                throw new AccessDeniedException($request->getRequestUri());
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
