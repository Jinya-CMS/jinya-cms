<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:08.
 */

namespace Jinya\Services\Users;

use Jinya\Entity\Artist\User;
use Jinya\Entity\Authentication\KnownDevice;

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
     * @param \Jinya\Entity\Artist\User $user
     * @param bool $ignorePassword
     * @return User
     */
    public function saveOrUpdate(User $user, bool $ignorePassword = false): User;

    /**
     * Activates the given user
     *
     * @param int $id
     * @return \Jinya\Entity\Artist\User
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
     * Sets the two factor code and sends the verification mail
     *
     * @param string $username
     * @param string $password
     */
    public function setTwoFactorCode(string $username, string $password): void;

    /**
     * Adds a new device code to the user
     *
     * @param string $username
     * @return string
     */
    public function addKnownDevice(string $username): string;

    /**
     * Deletes the given known device
     *
     * @param string $username
     * @param string $deviceCode
     * @return void
     */
    public function deleteKnownDevice(string $username, string $deviceCode): void;

    /**
     * Gets all known devices for the given user
     *
     * @param string $username
     * @return KnownDevice[]
     */
    public function getKnownDevices(string $username): array;
}
