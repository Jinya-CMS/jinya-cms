<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 28.02.2018
 * Time: 07:53
 */

namespace Jinya\Controller\Api\User;

use Jinya\Entity\Artist\User;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProfilePictureController extends BaseUserController
{
    /**
     * @Route("/api/user/{id}/profilepicture", methods={"GET"}, name="api_user_profilepicture_get")
     *
     * @param int $id
     * @param UserServiceInterface $userService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function getProfilePictureAction(
        int $id,
        UserServiceInterface $userService,
        MediaServiceInterface $mediaService
    ): Response {
        /** @var $data User|array */
        [$data, $status] = $this->tryExecute(static function () use ($id, $userService) {
            $user = $userService->get($id);
            if (empty($user->getProfilePicture())) {
                throw new FileNotFoundException($user->getEmail());
            }

            return $user;
        });

        if (200 !== $status) {
            return $this->json($data, $status);
        }

        return $this->file(
            $mediaService->getMedia($data->getProfilePicture()),
            $data->getArtistName() . '.jpg'
        );
    }

    /**
     * @Route("/api/user/{id}/profilepicture", methods={"PUT"}, name="api_user_profilepicture_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param Request $request
     * @param UserServiceInterface $userService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function putProfilePictureAction(
        int $id,
        Request $request,
        UserServiceInterface $userService,
        MediaServiceInterface $mediaService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use (
            $id,
            $request,
            $userService,
            $mediaService
        ) {
            if ($this->isCurrentUser($id) || $this->isGranted('ROLE_SUPER_ADMIN')) {
                $picture = $request->getContent(true);
                $picturePath = $mediaService->saveMedia($picture, MediaServiceInterface::PROFILE_PICTURE);
                $user = $userService->get($id);

                $user->setProfilePicture($picturePath);

                $userService->saveOrUpdate($user, true);
            }

            throw $this->createAccessDeniedException();
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/user/{id}/profilepicture", methods={"DELETE"}, name="api_user_profilepicture_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param UserServiceInterface $userService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteProfilePictureAction(
        int $id,
        UserServiceInterface $userService,
        MediaServiceInterface $mediaService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($id, $userService, $mediaService) {
            if ($this->isCurrentUser($id) || $this->isGranted('ROLE_SUPER_ADMIN')) {
                $user = $userService->get($id);
                $mediaService->deleteMedia($user->getProfilePicture());
                $user->setProfilePicture('');
                $userService->saveOrUpdate($user, true);
            } else {
                throw $this->createAccessDeniedException();
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
