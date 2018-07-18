<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 28.02.2018
 * Time: 08:17
 */

namespace Jinya\Controller\Api\Account;

use Jinya\Entity\Artist\User;
use Jinya\Formatter\User\UserFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Framework\Security\Api\ApiKeyToolInterface;
use Jinya\Services\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function uniqid;

class AccountController extends BaseApiController
{
    /**
     * @Route("/api/2fa", methods={"POST"}, name="api_account_2fa")
     *
     * @param UserServiceInterface $userService
     * @return Response
     */
    public function twoFactorAction(UserServiceInterface $userService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($userService) {
            $username = $this->getValue('username', '');
            $password = $this->getValue('password', '');

            $userService->setAndSendTwoFactorCode($username, $password);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/login", methods={"POST"}, name="api_account_login")
     *
     * @param ApiKeyToolInterface $apiKeyTool
     * @param UserServiceInterface $userService
     * @return Response
     */
    public function loginAction(ApiKeyToolInterface $apiKeyTool, UserServiceInterface $userService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($apiKeyTool, $userService) {
            $username = $this->getValue('username', '');
            $password = $this->getValue('password', '');
            $twoFactorToken = $this->getValue('twoFactorCode', '');
            $deviceCode = $this->getHeader('JinyaDeviceCode', '');

            $user = $userService->getUser($username, $password, $twoFactorToken, $deviceCode);
            $newDeviceCode = $userService->addKnownDevice($username);

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
     *
     * @param Request $request
     * @param ApiKeyToolInterface $apiKeyTool
     * @return Response
     */
    public function checkLoginAction(Request $request, ApiKeyToolInterface $apiKeyTool): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new Response('', Response::HTTP_NO_CONTENT);
        } else {
            $apiKeyTool->invalidate($request->headers->get('JinyaApiKey'));

            return new Response('', Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/api/logout", methods={"DELETE"}, name="api_account_logout")
     *
     * @param string $key
     * @param ApiKeyToolInterface $apiKeyTool
     * @return Response
     */
    public function logoutAction(string $key, ApiKeyToolInterface $apiKeyTool): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($key, $apiKeyTool) {
            $apiKeyTool->invalidate($key);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/account", methods={"GET"}, name="api_account_get")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     *
     * @param UserServiceInterface $userService
     * @param UserFormatterInterface $userFormatter
     * @return Response
     */
    public function getAction(UserServiceInterface $userService, UserFormatterInterface $userFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($userService, $userFormatter) {
            $user = $userService->get($this->getUser()->getId());

            return $userFormatter
                ->init($user)
                ->profile()
                ->roles()
                ->createdPages()
                ->createdGalleries()
                ->createdForms()
                ->createdArtworks()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/account", methods={"PUT"}, name="api_account_put")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     *
     * @param UserServiceInterface $userService
     * @return Response
     */
    public function putDataAction(UserServiceInterface $userService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($userService) {
            $user = $userService->get($this->getUser()->getId());
            $firstname = $this->getValue('firstname', $user->getFirstname());
            $lastname = $this->getValue('lastname', $user->getLastname());
            $email = $this->getValue('email', $user->getEmail());

            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);

            $userService->saveOrUpdate($user);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/account/password", methods={"PUT"}, name="api_account_password_put")
     * @IsGranted("IS_AUTHENTICATED_FULLY", statusCode=401)
     *
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param UserServiceInterface $userService
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     * @throws \Jinya\Exceptions\EmptyBodyException
     * @throws \Jinya\Exceptions\InvalidContentTypeException
     */
    public function putPasswordAction(UserPasswordEncoderInterface $userPasswordEncoder, UserServiceInterface $userService, UrlGeneratorInterface $urlGenerator): Response
    {
        $confirmToken = $this->getValue('token', uniqid());
        /** @var User $user */
        $user = $this->getUser();

        if (!empty($confirmToken) && $user->getConfirmationToken() === $confirmToken) {
            list($data, $status) = $this->tryExecute(function () use ($user, $userService) {
                $userService->changePassword($user->getId(), $this->getValue('password'));
            }, Response::HTTP_NO_CONTENT);

            return $this->json($data, $status);
        } else {
            list($data, $status) = $this->tryExecute(function () use ($urlGenerator, $confirmToken, $user, $userService, $userPasswordEncoder) {
                $user = $userService->get($user->getId());
                if ($userPasswordEncoder->isPasswordValid($user, $this->getValue('old_password'))) {
                    $user->setConfirmationToken($confirmToken);

                    $userService->saveOrUpdate($user, false);

                    return [
                        'url' => $urlGenerator->generate('api_account_password_put'),
                        'token' => $confirmToken,
                    ];
                } else {
                    throw new AccessDeniedException();
                }
            });

            return $this->json($data, $status);
        }
    }
}
