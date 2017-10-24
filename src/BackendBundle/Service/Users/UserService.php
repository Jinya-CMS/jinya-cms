<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:09
 */

namespace BackendBundle\Service\Users;


use AppKernel;
use BackendBundle\Entity\User;
use BackendBundle\Form\UserData;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\UserManipulator;
use const DIRECTORY_SEPARATOR;

class UserService implements UserServiceInterface
{

    /** @var UserManagerInterface */
    private $userManager;

    /** @var EntityManager */
    private $entityManager;

    /** @var UserManipulator */
    private $userManipulator;

    /** @var AppKernel */
    private $kernel;

    /**
     * UserService constructor.
     * @param UserManagerInterface $userManager
     * @param EntityManager $entityManager
     * @param UserManipulator $userManipulator
     * @param AppKernel $kernel
     */
    public function __construct(UserManagerInterface $userManager, EntityManager $entityManager, UserManipulator $userManipulator, $kernel)
    {
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $this->userManipulator = $userManipulator;
        $this->kernel = $kernel;
    }

    /**
     * @param int $offset
     * @param int $count
     * @return array
     */
    public function getAllUsers(int $offset, int $count = 10): array
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        return array_map(function (User $user) {
            return $this->convertUserToUserData($user);
        }, $userRepository->findBy([], null, $count, $offset));
    }

    /**
     * @param User $user
     * @return UserData
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

        return $userData;
    }

    public function deleteUser(int $id)
    {
        $user = $this->entityManager->find(User::class, $id);
        $this->entityManager->detach($user);
        $this->entityManager->flush();
    }

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
        $user->setUsername($userData->getUsername());
        $user->setEmail($userData->getEmail());
        $user->setPlainPassword($userData->getPassword());
        $user->setEnabled($userData->isActive());
        $user->setSuperAdmin($userData->isSuperAdmin());
        $user->setFirstname($userData->getFirstname());
        $user->setLastname($userData->getLastname());

        if ($userData->getProfilePicture()) {
            $user->setProfilePicture($this->moveProfilePicture($userData));
        }

        if ($userData->isWriter()) {
            $user->addRole(User::ROLE_WRITER);
        }
        if ($userData->isAdmin()) {
            $user->addRole(User::ROLE_ADMIN);
        }
    }

    private function moveProfilePicture(UserData $userData): string
    {
        $rootPath = $this->kernel->getProjectDir();
        $file = $userData->getProfilePicture();
        $movedFile = $file->move($rootPath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'profilepictures');

        return $movedFile->getFilename();
    }

    public function createUser(UserData $userData): User
    {
        /** @var User $user */
        $user = $this->userManager->createUser();
        $this->fillUserFromUserData($userData, $user);
        $this->userManager->updateUser($user);

        return $user;
    }

    public function activateUser(int $id): User
    {
        /** @var User $user */
        $user = $this->entityManager->find(User::class, $id);

        $user->setEnabled(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function deactivateUser(int $id): User
    {
        /** @var User $user */
        $user = $this->entityManager->find(User::class, $id);

        $user->setEnabled(false);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function getUser(int $id): UserData
    {
        /** @var User $user */
        $user = $this->entityManager->find(User::class, $id);

        return $this->convertUserToUserData($user);
    }

    public function grantRole(int $userId, string $role)
    {
        $this->userManipulator->addRole($this->getUsernameById($userId), $role);
    }

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

    public function revokeRole(int $userId, string $role)
    {
        $this->userManipulator->removeRole($this->getUsernameById($userId), $role);
    }
}



