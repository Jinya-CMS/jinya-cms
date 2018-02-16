<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 30.10.2017
 * Time: 17:11
 */

namespace Jinya\Form\Backend;


use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordData
{
    /**
     * @Assert\NotBlank(message="backend.users.password.not_blank")
     *
     * @var string
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
    public function setPassword($password)
    {
        $this->password = $password;
    }
}