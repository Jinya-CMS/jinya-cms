<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 15.07.18
 * Time: 17:20
 */

namespace Jinya\Controller\Api\Account;


use Jinya\Framework\BaseApiController;
use Jinya\Services\Users\UserServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KnownDeviceController extends BaseApiController
{

    /**
     * @Route("/api/account/known_device", methods={"GET"}, name="api_known_device_get_all")
     *
     * @param UserServiceInterface $userService
     * @return Response
     */
    public function getAllAction(UserServiceInterface $userService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($userService) {
            return [
                'success' => true,
                'items' => $userService->getKnownDevices($this->getUser()->getEmail())
            ];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/account/known_device/{key}", methods={"DELETE"}, name="api_known_device_get_all")
     *
     * @param string $key
     * @param UserServiceInterface $userService
     * @return Response
     */
    public function deleteAction(string $key, UserServiceInterface $userService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($key, $userService) {
            $userService->deleteKnownDevice($this->getUser()->getEmail(), $key);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}