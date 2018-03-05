<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 27.02.2018
 * Time: 22:29
 */

namespace Jinya\Controller\Api\User;


use Jinya\Entity\User;
use Jinya\Framework\BaseApiController;

class BaseUserController extends BaseApiController
{
    /**
     * Checks whether the given user is the currently logged in user
     *
     * @param int $id
     * @return bool
     */
    protected function isCurrentUser(int $id): bool
    {
        $user = $this->getUser();
        return $user instanceof User && $user->getId() === $id;
    }
}