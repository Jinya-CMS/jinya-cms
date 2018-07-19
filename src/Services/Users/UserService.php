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
use Jinya\Entity\Artist\User;
use Jinya\Entity\Authentication\KnownDevice;
use Jinya\Framework\Security\Api\ApiKeyToolInterface;
use Jinya\Framework\Security\UnknownDeviceException;
use Swift_Mailer;
use Swift_Message;
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

    /** @var Swift_Mailer */
    private $swift;

    /** @var string */
    private $mailerSender;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param ApiKeyToolInterface $apiKeyTool
     * @param Swift_Mailer $swift
     * @param string $mailerSender
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, ApiKeyToolInterface $apiKeyTool, Swift_Mailer $swift, string $mailerSender)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->apiKeyTool = $apiKeyTool;
        $this->swift = $swift;
        $this->mailerSender = $mailerSender;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function activate(int $id): User
    {
        /** @var \Jinya\Entity\Artist\User $user */
        $user = $this->entityManager->find(User::class, $id);

        $user->setEnabled(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function get(int $id): User
    {
        return $this->entityManager->find(User::class, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function grantRole(int $userId, string $role): void
    {
        $user = $this->entityManager->find(User::class, $userId);
        $user->addRole($role);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function revokeRole(int $userId, string $role): void
    {
        if (User::ROLE_SUPER_ADMIN !== $role || !$this->isLastSuperAdmin()) {
            $user = $this->entityManager->find(User::class, $userId);
            $user->removeRole($role);
            $this->entityManager->flush();
        }
    }

    /**
     * {@inheritdoc}
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
     * @param \Jinya\Entity\Artist\User $user
     * @param bool $ignorePassword
     * @return \Jinya\Entity\Artist\User
     */
    public function saveOrUpdate(User $user, bool $ignorePassword = false): User
    {
        if (!$ignorePassword) {
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPassword()));
        }

        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($user)) {
            if (!$this->entityManager->isOpen()) {
                /* @noinspection PhpUndefinedMethodInspection */
                $this->entityManager = $this->entityManager->create(
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
     * @param string $username
     * @param string $password
     * @param string $twoFactorCode
     * @param string $deviceCode
     * @return User
     * @throws UnknownDeviceException
     * @throws BadCredentialsException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUser(string $username, string $password, string $twoFactorCode, string $deviceCode): User
    {
        try {
            $user = $this->getUserByEmail($username);
        } catch (Exception $e) {
            throw new BadCredentialsException($e->getMessage(), $e);
        }

        if (!$this->userPasswordEncoder->isPasswordValid($user, $password)) {
            throw new BadCredentialsException('Invalid username or password');
        } elseif (!empty($deviceCode) && !$this->isValidDevice($username, $deviceCode)) {
            throw new UnknownDeviceException();
        } elseif (empty($deviceCode) && $twoFactorCode !== $user->getTwoFactorToken()) {
            throw new BadCredentialsException('No two factor code provided');
        }

        return $user;
    }

    /**
     * @param string $email
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getUserByEmail(string $email): User
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
     * @param string $username
     * @param string $deviceCode
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
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

    /**
     * Sets the two factor code and sends the verification mail
     *
     * @param string $username
     * @param string $password
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function setAndSendTwoFactorCode(string $username, string $password): void
    {
        $user = $this->getUserByEmail($username);
        $code = '';
        for ($i = 0; $i < 6; ++$i) {
            try {
                $code .= random_int(0, 9);
            } catch (Exception $e) {
                srand(time());
                $code .= rand(0, 9);
            }
        }
        $user->setTwoFactorToken($code);
        $this->entityManager->flush();

        /** @var Swift_Message $message */
        $message = $this->swift->createMessage('message');
        $message->addTo($user->getEmail());
        $message->setSubject('Your two factor code');
        $message->setBody($this->formatBody($user), 'text/html');
        $message->setFrom($this->mailerSender);
        $this->swift->send($message);
    }

    private function formatBody(User $user): string
    {
        $name = $user->getFirstname() . ' ' . $user->getLastname();
        $code = $user->getTwoFactorToken();

        return "<html>
<head></head>
<body style='font-family: -apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,\"Helvetica Neue\",Arial,sans-serif,\"Apple Color Emoji\",\"Segoe UI Emoji\",\"Segoe UI Symbol\"'>
    <table style='width: 100%; height: 100%;'>
    <tr>
        <td colspan='3' style='height: 15%;'></td>
    </tr>
    <tr>
    <td style='width: 35%;'></td>
    <td style='width: 30%;'>
        <p style='margin-top: 15%;'>
            Hello $name,<br /><br />
            you tried to login from a new device, please verify with this code that this was actually you.
        </p>
        <p style='text-align: center;'>
            <code style='background: #EFEFEF; padding: 6pt; text-align: center; font-size: 16pt; font-family: Consolas, monospace'>$code</code>
        </p>
        <p>
            Greetings,<br />
            Your Jinya Team
        </p>
    </td>
    <td style='width: 35%;'></td>
</tr>
</table>
</body>
</html>";
    }

    /**
     * Adds a new device code to the user
     *
     * @param string $username
     * @return string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws Exception
     */
    public function addKnownDevice(string $username): string
    {
        $user = $this->getUserByEmail($username);
        $knownDevice = new KnownDevice();
        $knownDevice->setUser($user);
        $knownDevice->setRemoteAddress($_SERVER['REMOTE_ADDR']);
        $knownDevice->setUserAgent($_SERVER['HTTP_USER_AGENT']);

        try {
            $code = bin2hex(random_bytes(20));
        } catch (Exception $exception) {
            $code = sha1(strval(time()));
        }

        $knownDevice->setKey($code);

        $this->entityManager->persist($knownDevice);
        $this->entityManager->flush();

        return $code;
    }

    /**
     * Gets all known devices for the given user
     *
     * @param string $username
     * @return KnownDevice[]
     */
    public function getKnownDevices(string $username): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('known_device')
            ->from(KnownDevice::class, 'known_device')
            ->join('known_device.user', 'user')
            ->where('user.email = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getResult();
    }

    /**
     * Deletes the given known device
     *
     * @param string $username
     * @param string $deviceCode
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteKnownDevice(string $username, string $deviceCode): void
    {
        $knownDevice = $this->entityManager->createQueryBuilder()
            ->select('known_device')
            ->from(KnownDevice::class, 'known_device')
            ->join('known_device.user', 'user')
            ->where('user.email = :username')
            ->andWhere('known_device.key = :deviceCode')
            ->setParameter('deviceCode', $deviceCode)
            ->setParameter('username', $username)
            ->getQuery()
            ->getSingleResult();

        $this->entityManager->remove($knownDevice);
        $this->entityManager->flush();
    }
}
