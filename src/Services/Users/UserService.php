<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:09.
 */

namespace Jinya\Services\Users;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Exception;
use Jinya\Entity\User;
use Jinya\Framework\Security\Api\ApiKeyToolInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class UserService implements UserServiceInterface
{

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UserPasswordEncoderInterface */
    private $userPasswordEncoder;
    /** @var ApiKeyToolInterface */
    private $apiKeyTool;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param ApiKeyToolInterface $apiKeyTool
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, ApiKeyToolInterface $apiKeyTool)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->apiKeyTool = $apiKeyTool;
    }

    /**
     * @inheritdoc
     */
    public function getAll(int $offset, int $count = 10, string $keyword): array
    {
        $queryBuilder = $this->createFilteredQueryBuilder($keyword);

        return $queryBuilder
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getResult();
    }

    /**
     * @param string $keyword
     * @return QueryBuilder
     */
    protected function createFilteredQueryBuilder(string $keyword): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('user')
            ->from(User::class, 'user')
            ->where($queryBuilder->expr()->like('user.firstname', ':keyword'))
            ->orWhere($queryBuilder->expr()->like('user.lastname', ':keyword'))
            ->orWhere($queryBuilder->expr()->like('user.email', ':keyword'))
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * @inheritdoc
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function delete(int $id): void
    {
        if (!$this->isLastSuperAdmin()) {
            $user = $this->entityManager->find(User::class, $id);
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function isLastSuperAdmin(): bool
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $roleSuperAdmin = User::ROLE_SUPER_ADMIN;

        $query = $queryBuilder
            ->select($queryBuilder->expr()->count('u'))
            ->from(User::class, 'u')
            ->where('u.roles like :role')
            ->andWhere('u.enabled = 1')
            ->setParameter('role', "%$roleSuperAdmin%")
            ->getQuery();

        return $query->getSingleScalarResult() < 1;
    }

    /**
     * @inheritdoc
     */
    public function activate(int $id): User
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deactivate(int $id): User
    {
        $user = $this->entityManager->find(User::class, $id);
        if (!$this->isLastSuperAdmin()) {
            $user->setEnabled(false);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function get(int $id): User
    {
        return $this->entityManager->find(User::class, $id);
    }

    /**
     * @inheritdoc
     */
    public function grantRole(int $userId, string $role): void
    {
        $user = $this->entityManager->find(User::class, $userId);
        $user->addRole($role);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function revokeRole(int $userId, string $role): void
    {
        if ($role !== User::ROLE_SUPER_ADMIN || !$this->isLastSuperAdmin()) {
            $user = $this->entityManager->find(User::class, $userId);
            $user->removeRole($role);
            $this->entityManager->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function changePassword(int $id, string $newPassword): void
    {
        $user = $this->entityManager->find(User::class, $id);
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $newPassword));
        $this->apiKeyTool->invalidateAll($id);
        $this->entityManager->flush();
    }

    /**
     * Counts all users
     *
     * @param string $keyword
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(string $keyword): int
    {
        $queryBuilder = $this->createFilteredQueryBuilder($keyword);

        return $queryBuilder
            ->select($queryBuilder->expr()->count('user'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Creates a user
     *
     * @param User $user
     * @return User
     */
    public function saveOrUpdate(User $user): User
    {
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPassword()));

        if ($this->entityManager->getUnitOfWork()->getEntityState($user) === UnitOfWork::STATE_NEW) {
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        return $user;
    }

    /**
     * Gets the user by username and password
     *
     * @param string $username
     * @param string $password
     * @return User
     */
    public function getUser(string $username, string $password): User
    {
        try {
            $user = $this->entityManager->createQueryBuilder()
                ->select('user')
                ->from(User::class, 'user')
                ->where('user.email = :username')
                ->setParameter('username', $username)
                ->getQuery()
                ->getSingleResult();
        } catch (Exception $e) {
            throw new BadCredentialsException();
        }

        if (!$this->userPasswordEncoder->isPasswordValid($user, $password)) {
            throw new BadCredentialsException();
        }

        return $user;
    }
}
