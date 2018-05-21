<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 10.03.2018
 * Time: 22:38
 */

namespace Jinya\Framework\Security\Api;


use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiKeyUserProvider implements UserProviderInterface
{

    /** @var ApiKeyToolInterface */
    private $apiKeyTool;
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * ApiKeyUserProvider constructor.
     * @param ApiKeyToolInterface $apiKeyTool
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ApiKeyToolInterface $apiKeyTool, EntityManagerInterface $entityManager)
    {
        $this->apiKeyTool = $apiKeyTool;
        $this->entityManager = $entityManager;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->entityManager->createQueryBuilder()
            ->select('user')
            ->from(User::class, 'user')
            ->where('user.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     * @return void
     */
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }

    public function getUsernameFromApiKey(string $key): string
    {
        $this->apiKeyTool->refreshToken($key);
        return $this->apiKeyTool->getUserByKey($key)->getEmail();
    }
}