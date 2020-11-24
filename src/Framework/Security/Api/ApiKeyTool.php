<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 10.03.2018
 * Time: 22:44
 */

namespace Jinya\Framework\Security\Api;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use Jinya\Entity\Artist\User;
use Jinya\Entity\Authentication\ApiKey;
use Jinya\Services\Configuration\ConfigurationServiceInterface;

class ApiKeyTool implements ApiKeyToolInterface
{
    private EntityManagerInterface $entityManager;

    private ConfigurationServiceInterface $configurationService;

    /**
     * ApiKeyTool constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigurationServiceInterface $configurationService
    ) {
        $this->entityManager = $entityManager;
        $this->configurationService = $configurationService;
    }

    /**
     * Creates an api key for the given user
     */
    public function createApiKey(User $user): string
    {
        $key = new ApiKey();
        $userId = $user->getId();
        $key->setUser($user);
        $key->setRemoteAddress($_SERVER['REMOTE_ADDR']);
        $key->setUserAgent($_SERVER['HTTP_USER_AGENT']);

        try {
            $key->setKey("jinya-api-token-$userId-" . bin2hex(random_bytes(20)));
        } catch (Exception $e) {
            $key->setKey(uniqid("jinya-api-token-$userId-", true));
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        return $key->getKey();
    }

    /**
     * Refreshes the validate since time
     *
     * @throws Exception
     */
    public function refreshToken(string $key): void
    {
        $this->entityManager->createQueryBuilder()
            ->update(ApiKey::class, 'key')
            ->set('key.validSince', ':date')
            ->setParameter('date', new DateTime())
            ->where('key.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->execute();
    }

    /**
     * Invalidates all tokens for the given user
     */
    public function invalidateAll(int $userId): void
    {
        $this->entityManager->createQueryBuilder()
            ->delete(ApiKey::class, 'key')
            ->where('key.user = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->execute();
    }

    /**
     * Invalidates the given api key if it is owned by the given user
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function invalidateKeyOfUser(string $username, string $key): void
    {
        $user = $this->getUserByKey($key);
        if ($user->getEmail() === $username) {
            $this->invalidate($key);
        }
    }

    /**
     * Gets the user for the given api key
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getUserByKey(string $key): User
    {
        return $this->entityManager->createQueryBuilder()
            ->select('user')
            ->from(User::class, 'user')
            ->join(ApiKey::class, 'key', Join::WITH, 'key.user = user')
            ->where('key.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Invalidates the given api key
     */
    public function invalidate(string $key): void
    {
        $this->entityManager->createQueryBuilder()
            ->delete(ApiKey::class, 'key')
            ->where('key.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->execute();
    }

    /**
     * Gets all api keys for the given user
     *
     * @throws Exception
     */
    public function getAllForUser(string $email): array
    {
        /** @var ApiKey[] $keys */
        $keys = $this->entityManager->createQueryBuilder()
            ->select('api_key')
            ->from(ApiKey::class, 'api_key')
            ->join('api_key.user', 'user')
            ->where('user.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();

        $result = [];

        foreach ($keys as $key) {
            if ($this->shouldInvalidate($key->getKey())) {
                $this->invalidate($key->getKey());
            } else {
                $result[] = $key;
            }
        }

        return $result;
    }

    /**
     * Checks whether the given api key should get invalidated
     *
     * @throws Exception
     */
    public function shouldInvalidate(string $key): bool
    {
        /** @var DateTime $validSince */
        $validSince = $this->entityManager->createQueryBuilder()
            ->select('key')
            ->from(ApiKey::class, 'key')
            ->where('key.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getSingleResult()
            ->getValidSince();

        $keyInvalidation = $this->configurationService->getConfig()->getInvalidateApiKeyAfter();
        $validSince->add(new DateInterval("PT${keyInvalidation}S"));

        return (new DateTime())->getTimestamp() > $validSince->getTimestamp();
    }

    /**
     * Check if the given key exists in the database
     */
    public function keyExists(string $key): bool
    {
        return null !== $this->entityManager->getRepository(ApiKey::class)->findOneBy(['key' => $key]);
    }
}
