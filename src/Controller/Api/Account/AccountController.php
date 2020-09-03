<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 28.02.2018
 * Time: 08:17
 */

namespace Jinya\Controller\Api\Account;

use Exception;
use Jinya\Exceptions\EmptyBodyException;
use Jinya\Exceptions\InvalidContentTypeException;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\User\UserFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Framework\Security\Api\ApiKeyToolInterface;
use Jinya\Services\Users\AuthenticationServiceInterface;
use Jinya\Services\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AccountController extends BaseApiController
{
    /**
     * @Route("/api/2fa", methods={"POST"}, name="api_account_2fa")
     */
    public function twoFactorAction(
        AuthenticationServiceInterface $authenticationService,
        UserServiceInterface $userService,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): Response {
        [$data, $status] = $this->tryExecute(function () use (
            $authenticationService,
            $userService,
            $userPasswordEncoder
        ) {
            $username = $this->getValue('username', '');
            $password = $this->getValue('password', '');

            $user = $userService->getUserByEmail($username);

            if (!$userPasswordEncoder->isPasswordValid($user, $password)) {
                throw new BadCredentialsException('Invalid username or password');
            }

            $authenticationService->setAndSendTwoFactorCode($username);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/login", methods={"POST"}, name="api_account_login")
     */
    public function loginAction(
        ApiKeyToolInterface $apiKeyTool,
        UserServiceInterface $userService,
        AuthenticationServiceInterface $authenticationService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($authenticationService, $apiKeyTool, $userService) {
            $username = $this->getValue('username', '');
            $password = $this->getValue('password', '');
            $twoFactorToken = $this->getValue('twoFactorCode', '');
            $deviceCode = $this->getHeader('JinyaDeviceCode', '');

            $user = $userService->getUser($username, $password, $twoFactorToken, $deviceCode);
            $newDeviceCode = $authenticationService->addKnownDevice($username);

            return [
                'apiKey' => $apiKeyTool->createApiKey($user),
                'deviceCode' => $newDeviceCode,
                'roles' => $user->getRoles(),
            ];
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/login", methods={"HEAD"}, name="account_login_check")
     */
    public function checkLoginAction(Request $request, ApiKeyToolInterface $apiKeyTool): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $apiKeyTool->invalidate($request->headers->get('JinyaApiKey'));

        return new Response('', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/api/account", methods={"GET"}, name="api_account_get")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     */
    public function getAction(UserServiceInterface $userService, UserFormatterInterface $userFormatter): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($userService, $userFormatter) {
            /** @noinspection NullPointerExceptionInspection */
            /** @noinspection NullPointerExceptionInspection */
            $user = $userService->get($this->getUser()->getId());

            return $userFormatter
                ->init($user)
                ->id()
                ->profile()
                ->roles()
                ->createdPages()
                ->createdForms()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/account", methods={"PUT"}, name="api_account_put")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     */
    public function putDataAction(UserServiceInterface $userService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($userService) {
            /** @noinspection NullPointerExceptionInspection */
            /** @noinspection NullPointerExceptionInspection */
            $user = $userService->get($this->getUser()->getId());
            $artistName = $this->getValue('artistName', $user->getLastname());
            $email = $this->getValue('email', $user->getEmail());

            $user->setArtistName($artistName);
            $user->setEmail($email);

            $userService->saveOrUpdate($user);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/account/password", methods={"PUT"}, name="api_account_password_put")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     *
     * @throws EmptyBodyException
     * @throws InvalidContentTypeException
     * @throws Exception
     */
    public function putPasswordAction(
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserServiceInterface $userService,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($userService) {
            $confirmToken = $this->getValue('token', base64_encode(random_bytes(10)));
            $user = $this->getUser();
            if (!empty($confirmToken) && $user->getConfirmationToken() === $confirmToken) {
                $password = $this->getValue('password');
                if (null === $password) {
                    throw new MissingFieldsException(['password' => 'api.account.field.password.missing']);
                }
                $userService->changePassword($user->getId(), $password);

                return 'submitted';
            }

            return 'error';
        }, Response::HTTP_NO_CONTENT);

        if ('submitted' === $data || Response::HTTP_NO_CONTENT !== $status) {
            return $this->json($data, $status);
        }

        [$data, $status] = $this->tryExecute(
            function () use ($urlGenerator, $userService, $userPasswordEncoder) {
                $confirmToken = $this->getValue('token', base64_encode(random_bytes(10)));
                $user = $userService->get($this->getUser()->getId());
                if ($userPasswordEncoder->isPasswordValid($user, $this->getValue('old_password', ''))) {
                    $user->setConfirmationToken($confirmToken);

                    $userService->saveOrUpdate($user);

                    return [
                        'url' => $urlGenerator->generate('api_account_password_put'),
                        'token' => $confirmToken,
                    ];
                }

                throw new AccessDeniedException();
            }
        );

        return $this->json($data, $status);
    }
}
