<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 10.03.2018
 * Time: 22:41
 */

namespace Jinya\Framework\Security\Api;


use Jinya\Entity\User;

interface ApiKeyToolInterface
{
    /**
     * Creates an api key for the given user
     *
     * @param User $user
     * @return string
     */
    public function createApiKey(User $user): string;

    /**
     * Gets the user for the given api key
     *
     * @param string $key
     * @return User
     */
    public function getUserByKey(string $key): User;

    /**
     * Invalidates the given api key
     *
     * @param string $key
     */
    public function invalidate(string $key): void;

    /**
     * Refreshes the validate since time
     *
     * @param string $key
     */
    public function refreshToken(string $key): void;

    /**
     * Checks whether the given api key should get invalidated
     *
     * @param string $key
     * @return bool
     */
    public function shouldInvalidate(string $key): bool;

    /**
     * Invalidates all tokens for the given user
     *
     * @param int $userId
     */
    public function invalidateAll(int $userId): void;
}