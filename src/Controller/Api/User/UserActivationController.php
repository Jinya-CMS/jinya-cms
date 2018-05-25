<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 27.02.2018
 * Time: 22:31.
 */

namespace Jinya\Controller\Api\User;

use Jinya\Services\Users\UserServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserActivationController extends BaseUserController
{
    /**
     * @Route("/api/user/{id}/activation", methods={"DELETE"}, name="api_user_activation_deactivate")
     *
     * @param int                  $id
     * @param UserServiceInterface $userService
     *
     * @return Response
     */
    public function deactivateAction(int $id, UserServiceInterface $userService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $userService) {
            if (!$this->isCurrentUser($id)) {
                $userService->deactivate($id);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/user/{id}/activation", methods={"PUT"}, name="api_user_activation_activate")
     *
     * @param int                  $id
     * @param UserServiceInterface $userService
     *
     * @return Response
     */
    public function activateAction(int $id, UserServiceInterface $userService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $userService) {
            $userService->activate($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
