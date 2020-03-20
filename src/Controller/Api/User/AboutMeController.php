<?php

namespace Jinya\Controller\Api\User;

use Jinya\Services\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutMeController extends BaseUserController
{
    /**
     * @Route("/api/user/{id}/about", methods={"GET"}, name="api_user_get_about")
     * @IsGranted("IS_AUTHENTICATED")
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
}
