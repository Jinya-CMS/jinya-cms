<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 27.02.2018
 * Time: 22:31
 */

namespace Jinya\Controller\Api\User;

use Jinya\Services\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserActivationController extends BaseUserController
{
    /**
     * @Route("/api/user/{id}/activation", methods={"DELETE"}, name="api_user_activation_deactivate")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function deactivateAction(int $id, UserServiceInterface $userService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($id, $userService) {
            if (!$this->isCurrentUser($id)) {
                $userService->deactivate($id);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/user/{id}/activation", methods={"PUT"}, name="api_user_activation_activate")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function activateAction(int $id, UserServiceInterface $userService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $userService) {
            $userService->activate($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
