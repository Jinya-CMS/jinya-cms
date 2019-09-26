<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 15.07.18
 * Time: 17:20
 */

namespace Jinya\Controller\Api\Account;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Users\AuthenticationServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KnownDeviceController extends BaseApiController
{
    /**
     * @Route("/api/account/known_device", methods={"GET"}, name="api_known_device_get_all")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     *
     * @param AuthenticationServiceInterface $authService
     * @return Response
     */
    public function getAllAction(AuthenticationServiceInterface $authService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($authService) {
            /* @noinspection NullPointerExceptionInspection */
            /* @noinspection NullPointerExceptionInspection */
            return [
                'success' => true,
                'items' => $authService->getKnownDevices($this->getUser()->getEmail()),
            ];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/account/known_device/{key}", methods={"DELETE"}, name="api_known_device_delete")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     *
     * @param string $key
     * @param AuthenticationServiceInterface $authService
     * @return Response
     */
    public function deleteAction(string $key, AuthenticationServiceInterface $authService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($key, $authService) {
            /* @noinspection NullPointerExceptionInspection */
            /* @noinspection NullPointerExceptionInspection */
            $authService->deleteKnownDevice($this->getUser()->getEmail(), $key);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
