<?php

namespace Jinya\Controller\Api\MyJinya;

use Jinya\Controller\Api\User\BaseUserController;
use Jinya\Services\Media\ConversionServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyJinyaController extends BaseUserController
{
    /**
     * @Route("/api/me", methods={"PUT"}, name="api_my_jinya_put")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function putProfileAction(UserServiceInterface $userService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($userService) {
            $email = $this->getValue('email');
            $artistName = $this->getValue('artistName');
            $aboutMe = $this->getValue('aboutMe');

            $user = $this->getUser();
            if ($user) {
                $user->setArtistName($artistName);
                $user->setEmail($email);
                $user->setAboutMe($aboutMe);

                $userService->saveOrUpdate($user);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/me/profilepicture", methods={"PUT"}, name="api_my_jinya_put_profile_picture")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function putProfilePictureAction(
        Request $request,
        UserServiceInterface $userService,
        MediaServiceInterface $mediaService,
        ConversionServiceInterface $conversionService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use (
            $request,
            $userService,
            $mediaService,
            $conversionService
        ) {
            $user = $this->getUser();
            if ($user) {
                $picture = $request->getContent();
                $file = $conversionService->convertImage($picture, IMAGETYPE_PNG);
                $path = $mediaService->saveMedia($file, MediaServiceInterface::PROFILE_PICTURE);
                $user->setProfilePicture($path);

                $userService->saveOrUpdate($user);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
