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
use Jinya\Entity\User;
use Jinya\Services\Media\MediaServiceInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService implements UserServiceInterface
{

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UserPasswordEncoderInterface */
    private $userPasswordEncoder;
    /** @var MediaServiceInterface */
    private $mediaService;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param MediaServiceInterface $mediaService
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, MediaServiceInterface $mediaService)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->mediaService = $mediaService;
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
        $userRepository = $this->entityManager->getRepository(User::class);
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $userRepository->createQueryBuilder('user');

        return $queryBuilder
            ->select('user')
            ->where($queryBuilder->expr()->like('user.firstname', ':keyword'))
            ->orWhere($queryBuilder->expr()->like('user.lastname', ':keyword'))
            ->orWhere($queryBuilder->expr()->like('user.email', ':keyword'))
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id): void
    {
        $user = $this->entityManager->find(User::class, $id);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
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
     */
    public function deactivate(int $id): User
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
     */
    public function revokeRole(int $userId, string $role): void
    {
        $user = $this->entityManager->find(User::class, $userId);
        $user->removeRole($role);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function changePassword(int $id, string $newPassword): void
    {
        $user = $this->entityManager->find(User::class, $id);
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $newPassword));
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
}
