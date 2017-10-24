<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 22.10.2017
 * Time: 20:21
 */

namespace BackendBundle\Form;


use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UserData
{
    /**
     * @var bool
     */
    private $active;
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
    /**
     * @var string
     * @Assert\NotBlank(message="backend.users.password.not_blank")
     */
    private $password;
    /**
     * @var bool
     */
    private $admin;
    /**
     * @var bool
     */
    private $superAdmin;
    /**
     * @var bool
     */
    private $writer;

    /** @var UploadedFile */
    private $profilePicture;

    /** @var int */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return UploadedFile
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param mixed $profilePicture
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
    }

    /**
     * @return bool
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
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
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin || false;
    }

    /**
     * @param bool $admin
     */
    public function setAdmin(bool $admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->superAdmin || false;
    }

    /**
     * @param bool $superAdmin
     */
    public function setSuperAdmin(bool $superAdmin)
    {
        $this->superAdmin = $superAdmin;
    }

    /**
     * @return bool
     */
    public function isWriter(): bool
    {
        return $this->writer || false;
    }

    /**
     * @param bool $writer
     */
    public function setWriter(bool $writer)
    {
        $this->writer = $writer;
    }
}