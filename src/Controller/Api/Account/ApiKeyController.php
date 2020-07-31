<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 17.07.18
 * Time: 09:39
 */

namespace Jinya\Controller\Api\Account;

use Jinya\Framework\BaseApiController;
use Jinya\Framework\Security\Api\ApiKeyToolInterface;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiKeyController extends BaseApiController
{
    /**
     * @Route("/api/account/api_key", methods={"GET"}, name="api_api_key_get_all")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     */
    public function getAllAction(
        ApiKeyToolInterface $apiKeyTool,
        ConfigurationServiceInterface $configurationService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($apiKeyTool, $configurationService) {
            /* @noinspection NullPointerExceptionInspection */
            /* @noinspection NullPointerExceptionInspection */
            return [
                'success' => true,
                'invalidateApiKeyAfter' => $configurationService->getConfig()->getInvalidateApiKeyAfter(),
                'items' => $apiKeyTool->getAllForUser($this->getUser()->getEmail()),
            ];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/account/api_key/{key}", methods={"DELETE"}, name="api_api_key_delete")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     */
    public function deleteAction(string $key, ApiKeyToolInterface $apiKeyTool): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($key, $apiKeyTool) {
            /* @noinspection NullPointerExceptionInspection */
            /* @noinspection NullPointerExceptionInspection */
            $apiKeyTool->invalidateKeyOfUser($this->getUser()->getEmail(), $key);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
