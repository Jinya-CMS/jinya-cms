<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:09.
 */

namespace Jinya\Services\Users;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Exception;
use Jinya\Entity\Artist\User;
use Jinya\Entity\Authentication\KnownDevice;
use Jinya\Framework\Security\Api\ApiKeyToolInterface;
use Jinya\Framework\Security\UnknownDeviceException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class UserService implements UserServiceInterface
{
    private EntityManagerInterface $entityManager;

    private UserPasswordEncoderInterface $userPasswordEncoder;

    private ApiKeyToolInterface $apiKeyTool;

    /**
     * UserService constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ApiKeyToolInterface $apiKeyTool
    ) {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->apiKeyTool = $apiKeyTool;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(string $keyword = ''): array
    {
        return $this->createFilteredQueryBuilder($keyword)
            ->getQuery()
            ->getResult();
    }

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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function delete(int $id): void
    {
        if (!$this->isLastSuperAdmin()) {
            $user = $this->get($id);
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NoResultException
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
     * {@inheritdoc}
     */
    public function get(int $id): User
    {
        return $this->entityManager->find(User::class, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function activate(int $id): User
    {
        $user = $this->get($id);

        $user->setEnabled(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * {@inheritdoc}
     * @throws NonUniqueResultException
     */
    public function deactivate(int $id): User
    {
        $user = $this->get($id);
        if (!$this->isLastSuperAdmin()) {
            $user->setEnabled(false);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function grantRole(int $userId, string $role): void
    {
        $user = $this->get($userId);
        $user->addRole($role);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     * @throws NonUniqueResultException
     */
    public function revokeRole(int $userId, string $role): void
    {
        if (User::ROLE_SUPER_ADMIN !== $role || !$this->isLastSuperAdmin()) {
            $user = $this->get($userId);
            $user->removeRole($role);
            $this->entityManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function changePassword(int $id, string $newPassword): void
    {
        $user = $this->get($id);
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $newPassword));
        $this->apiKeyTool->invalidateAll($id);
        $this->entityManager->flush();
    }

    /**
     * Counts all users
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NoResultException
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
     */
    public function saveOrUpdate(User $user, bool $ignorePassword = false): User
    {
        if (!$ignorePassword) {
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPassword()));
        }

        $state = $this->entityManager->getUnitOfWork()->getEntityState($user);
        if (UnitOfWork::STATE_MANAGED !== $state && UnitOfWork::STATE_REMOVED !== $state) {
            if (!$this->entityManager->isOpen()) {
                $this->entityManager = $this->entityManager::create(
                    $this->entityManager->getConnection(),
                    $this->entityManager->getConfiguration()
                );
            }
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        return $user;
    }

    /**
     * Gets the user by username and password
     *
     * @throws UnknownDeviceException
     * @throws BadCredentialsException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getUser(string $username, string $password, string $twoFactorCode, string $deviceCode): User
    {
        try {
            $user = $this->getUserByEmail($username);
        } catch (Exception $e) {
            throw new BadCredentialsException($e->getMessage(), 0, $e);
        }

        if (!$this->userPasswordEncoder->isPasswordValid($user, $password)) {
            throw new BadCredentialsException('Invalid username or password');
        }

        if (!empty($deviceCode) && !$this->isValidDevice($username, $deviceCode)) {
            throw new UnknownDeviceException('This device is unknown');
        }

        if (empty($deviceCode) && $twoFactorCode !== $user->getTwoFactorToken()) {
            throw new BadCredentialsException('No two factor code provided');
        }

        return $user;
    }

    /**
     * Gets the user with the given email address
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getUserByEmail(string $email): User
    {
        return $this->entityManager->createQueryBuilder()
            ->select('user')
            ->from(User::class, 'user')
            ->where('user.email = :username')
            ->setParameter('username', $email)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Checks whether the given device code belongs to the given user
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function isValidDevice(string $username, string $deviceCode): bool
    {
        return $this->entityManager->createQueryBuilder()
            ->select('COUNT(known_device)')
            ->from(KnownDevice::class, 'known_device')
            ->join('known_device.user', 'user')
            ->where('user.email = :username')
            ->andWhere('known_device.key = :deviceCode')
            ->setParameter('deviceCode', $deviceCode)
            ->setParameter('username', $username)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
