<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:08.
 */

namespace Jinya\Services\Users;

use Jinya\Entity\Artist\User;

interface UserServiceInterface
{
    /**
     * Gets the user specified by the id
     *
     * @param int $id
     * @return User
     */
    public function get(int $id): User;

    /**
     * Gets all users in the given range
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return User[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

    /**
     * Counts all users
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword): int;

    /**
     * Deletes the given user
     *
     * @param int $id
     */
    public function delete(int $id): void;

    /**
     * Changes the password for the given user
     *
     * @param int $id
     * @param string $newPassword
     */
    public function changePassword(int $id, string $newPassword): void;

    /**
     * Creates a user
     *
     * @param User $user
     * @param bool $ignorePassword
     * @return User
     */
    public function saveOrUpdate(User $user, bool $ignorePassword = false): User;

    /**
     * Activates the given user
     *
     * @param int $id
     * @return User
     */
    public function activate(int $id): User;

    /**
     * Deactivates the given user
     *
     * @param int $id
     * @return User
     */
    public function deactivate(int $id): User;

    /**
     * Grants the given role for the given user
     *
     * @param int $userId
     * @param string $role
     */
    public function grantRole(int $userId, string $role): void;

    /**
     * Revokes the given role for the given user
     *
     * @param int $userId
     * @param string $role
     */
    public function revokeRole(int $userId, string $role): void;

    /**
     * Gets the user by username and password
     *
     * @param string $username
     * @param string $password
     * @param string $twoFactorCode
     * @param string $deviceCode
     * @return User
     */
    public function getUser(string $username, string $password, string $twoFactorCode, string $deviceCode): User;

    /**
     * Gets the user with the given email address
     *
     * @param string $email
     * @return User
     */
    public function getUserByEmail(string $email): User;
}
