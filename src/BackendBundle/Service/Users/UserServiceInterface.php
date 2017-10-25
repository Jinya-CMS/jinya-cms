<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:08
 */

namespace BackendBundle\Service\Users;


use BackendBundle\Entity\User;
use BackendBundle\Form\AddUserData;
use BackendBundle\Form\UserData;

interface UserServiceInterface
{
    public function getUser(int $id);

    public function getAllUsers(int $offset, int $count = 10);

    public function deleteUser(int $id);

    public function updateUser(int $id, UserData $userData);

    public function changePassword(int $id, string $newPassword);

    public function createUser(AddUserData $userData): User;

    public function activateUser(int $id);

    public function deactivateUser(int $id);

    public function grantRole(int $userId, string $role);

    public function revokeRole(int $userId, string $role);
}