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
use Doctrine\ORM\Query\Expr\Join;
use Jinya\Entity\ApiKey;
use Jinya\Entity\User;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use function uniqid;

class ApiKeyTool implements ApiKeyToolInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ConfigurationServiceInterface */
    private $configurationService;

    /**
     * ApiKeyTool constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ConfigurationServiceInterface $configurationService
     */
    public function __construct(EntityManagerInterface $entityManager, ConfigurationServiceInterface $configurationService)
    {
        $this->entityManager = $entityManager;
        $this->configurationService = $configurationService;
    }

    /**
     * Creates an api key for the given user
     *
     * @param User $user
     *
     * @return string
     */
    public function createApiKey(User $user): string
    {
        $key = new ApiKey();
        $userId = $user->getId();
        $key->setUser($user);
        $key->setKey(uniqid("jinya-api-token-$userId-"));

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        return $key->getKey();
    }

    /**
     * Gets the user for the given api key
     *
     * @param string $key
     *
     * @return User
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     *
     * @param string $key
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
     * Refreshes the validate since time
     *
     * @param string $key
     */
    public function refreshToken(string $key): void
    {
        $this->entityManager->createQueryBuilder()
            ->update(ApiKey::class, 'key')
            ->set('key.validSince', ':date')
            ->setParameter('date', new DateTime())
            ->getQuery()
            ->execute();
    }

    /**
     * Checks whether the given api key should get invalidated
     *
     * @param string $key
     *
     * @return bool
     *
     * @throws \Exception
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
     * Invalidates all tokens for the given user
     *
     * @param int $userId
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
}
