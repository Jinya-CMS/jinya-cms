<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 25.10.2017
 * Time: 18:24
 */

namespace BackendBundle\Form;


use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    public function getProfilePicture(): UploadedFile
    {
        return $this->profilePicture;
    }

    /**
     * @param UploadedFile $profilePicture
     */
    public function setProfilePicture(UploadedFile $profilePicture)
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