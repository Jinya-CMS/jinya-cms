<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:09.
 */

namespace ServiceBundle\Services\Users;

use BackendBundle\Form\AddUserData;
use BackendBundle\Form\UserData;
use DataBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Exception;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\UserManipulator;
use ServiceBundle\Services\Media\MediaServiceInterface;

class UserService implements UserServiceInterface
{
    /** @var UserManagerInterface */
    private $userManager;

    /** @var EntityManager */
    private $entityManager;

    /** @var UserManipulator */
    private $userManipulator;

    /** @var MediaServiceInterface */
    private $mediaService;

    /**
     * UserService constructor.
     * @param UserManagerInterface $userManager
     * @param EntityManager $entityManager
     * @param UserManipulator $userManipulator
     * @param MediaServiceInterface $mediaService
     */
    public function __construct(UserManagerInterface $userManager, EntityManager $entityManager, UserManipulator $userManipulator, MediaServiceInterface $mediaService)
    {
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $this->userManipulator = $userManipulator;
        $this->mediaService = $mediaService;
    }

    /**
     * @inheritdoc
     */
    public function getAllUsers(int $offset, int $count = 10): array
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        return array_map(function (User $user) {
            return $this->convertUserToUserData($user);
        }, $userRepository->findBy([], null, $count, $offset));
    }

    /**
     * @inheritdoc
     */
    private function convertUserToUserData(User $user): UserData
    {
        $userData = new UserData();
        $userData->setEmail($user->getEmail());
        $userData->setFirstname($user->getFirstname());
        $userData->setLastname($user->getLastname());
        $userData->setUsername($user->getUsername());
        $userData->setAdmin($user->hasRole(User::ROLE_ADMIN));
        $userData->setWriter($user->hasRole(User::ROLE_WRITER));
        $userData->setSuperAdmin($user->isSuperAdmin());
        $userData->setActive($user->isEnabled());
        $userData->setId($user->getId());
        $userData->setProfilePicture($user->getProfilePicture());

        return $userData;
    }

    /**
     * @inheritdoc
     */
    public function deleteUser(int $id): void
    {
        $user = $this->entityManager->find(User::class, $id);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function updateUser(int $id, UserData $userData): User
    {
        /** @var User $user */
        $user = $this->userManager->findUserBy(['id' => $id]);
        $this->fillUserFromUserData($userData, $user);
        $this->userManager->updateUser($user);

        return $user;
    }

    /**
     * @param UserData $userData
     * @param User $user
     */
    private function fillUserFromUserData(UserData $userData, User $user)
    {
        if ($userData->getUsername() !== null) {
            $user->setUsername($userData->getUsername());
        }
        if ($userData->getEmail() !== null) {
            $user->setEmail($userData->getEmail());
        }
        if ($userData->isActive() !== null) {
            $user->setEnabled($userData->isActive());
        }
        if ($userData->isSuperAdmin() !== null) {
            $user->setSuperAdmin($userData->isSuperAdmin());
        }
        if ($userData->getFirstname() !== null) {
            $user->setFirstname($userData->getFirstname());
        }
        if ($userData->getLastname() !== null) {
            $user->setLastname($userData->getLastname());
        }
        if ($userData->getProfilePicture()) {
            $user->setProfilePicture($this->moveProfilePicture($userData));
        }

        if ($userData->isWriter()) {
            $user->addRole(User::ROLE_WRITER);
        } elseif ($userData->isWriter() === false) {
            $user->removeRole(User::ROLE_WRITER);
        }
        if ($userData->isAdmin()) {
            $user->addRole(User::ROLE_ADMIN);
        } elseif ($userData->isAdmin() === false) {
            $user->removeRole(User::ROLE_ADMIN);
        }
    }

    private function moveProfilePicture(UserData $userData): string
    {
        $file = $userData->getProfilePicture();

        try {
            return $this->mediaService->saveMedia($file, MediaServiceInterface::PROFILE_PICTURE);
        } catch (Exception $ex) {
            return '';
        }
    }

    /**
     * @inheritdoc
     */
    public function createUser(AddUserData $userData): User
    {
        /** @var User $user */
        $user = $this->userManager->createUser();
        $this->fillUserFromUserData($userData, $user);
        $user->setPlainPassword($userData->getPassword());
        $this->userManager->updateUser($user);

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function activateUser(int $id): User
    {
        /** @var User $user */
        $user = $this->entityManager->find(User::class, $id);

        $user->setEnabled(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function deactivateUser(int $id): User
    {
        /** @var User $user */
        $user = $this->entityManager->find(User::class, $id);

        $user->setEnabled(false);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getUser(int $id): UserData
    {
        /** @var User $user */
        $user = $this->entityManager->find(User::class, $id);

        return $this->convertUserToUserData($user);
    }

    /**
     * @inheritdoc
     */
    public function grantRole(int $userId, string $role): void
    {
        $this->userManipulator->addRole($this->getUsernameById($userId), $role);
    }

    /**
     * @param int $userId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getUsernameById(int $userId)
    {
        return $this->entityManager->createQueryBuilder()
            ->select('user.username')
            ->from(User::class, 'user')
            ->where('user.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function revokeRole(int $userId, string $role): void
    {
        $this->userManipulator->removeRole($this->getUsernameById($userId), $role);
    }

    /**
     * @inheritdoc
     */
    public function changePassword(int $id, string $newPassword): void
    {
        $this->userManipulator->changePassword($this->getUsernameById($id), $newPassword);
    }
}
