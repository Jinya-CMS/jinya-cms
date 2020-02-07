<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 10.03.2018
 * Time: 22:35
 */

namespace Jinya\Framework\Security\Api;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiKeyAuthenticator extends AbstractGuardAuthenticator
{
    private const JINYA_API_KEY = 'JinyaApiKey';

    /** @var ApiKeyToolInterface */
    private $apiKeyTool;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * ApiKeyAuthenticator constructor.
     * @param ApiKeyToolInterface $apiKeyTool
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ApiKeyToolInterface $apiKeyTool,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ) {
        $this->apiKeyTool = $apiKeyTool;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return $request->headers->has(self::JINYA_API_KEY);
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->headers->get(self::JINYA_API_KEY),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiToken = $credentials['token'];

        if (null === $apiToken || !$this->apiKeyTool->keyExists($apiToken)) {
            return null;
        }

        try {
            if ($this->apiKeyTool->shouldInvalidate($apiToken)) {
                $this->apiKeyTool->invalidate($apiToken);

                throw new CustomUserMessageAuthenticationException($this->translator->trans(
                    'api.state.401.expired',
                    ['apiKey' => $apiToken]
                ));
            }
        } catch (Exception $exception) {
            $this->logger->warning($exception->getMessage());
            $this->logger->warning($exception->getTraceAsString());

            throw new CustomUserMessageAuthenticationException($this->translator->trans(
                'api.state.401.generic',
                ['apiKey' => $apiToken]
            ));
        }

        // if a User object, checkCredentials() is called
        return $this->apiKeyTool->getUserByKey($apiToken);
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
