<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.10.2017
 * Time: 20:34
 */

namespace BackendBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

class LoginModel
{
    /**
     * @var ?string
     * @Assert\NotBlank(message="login.email.required")
     * @Assert\Email(message="login.email.not_valid")
     */
    private $email;
    /**
     * @var ?string
     * @Assert\NotBlank(message="login.password.required")
     */
    private $password;

    /**
     * @return null|string
     */
    public function getEmail():?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return null|string
     */
    public function getPassword():?string
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