<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 25.10.2017
 * Time: 18:24.
 */

namespace Jinya\Form\Install;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class AdminData
{
    /**
     * @var string
     * @Assert\NotBlank(message="backend.users.firstname.not_blank")
     */
    private $firstname;
    /**
     * @var string
     * @Assert\NotBlank(message="backend.users.lastname.not_blank")
     */
    private $lastname;
    /**
     * @var string
     * @Assert\Email(message="backend.users.email.invalid")
     * @Assert\NotBlank(message="backend.users.email.not_blank")
     */
    private $email;
    /**
     * @var string
     * @Assert\NotBlank(message="backend.users.username.not_blank")
     */
    private $username;

    /** @var UploadedFile|string */
    private $profilePicture;

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

    /**
     * @return UploadedFile|string
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param UploadedFile|string $profilePicture
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
    }


    /**
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
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
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }
}
