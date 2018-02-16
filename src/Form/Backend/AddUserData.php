<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 22.10.2017
 * Time: 20:21.
 */

namespace Jinya\Form\Backend;

use Symfony\Component\Validator\Constraints as Assert;

class AddUserData extends UserData
{
    /**
     * @var string
     * @Assert\NotBlank(message="backend.users.password.not_blank")
     */
    private $password;

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
}
