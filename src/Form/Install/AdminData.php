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
     * @Assert\NotBlank(message="install.users.artist_name.not_blank")
     */
    private $artistName;

    /**
     * @var string
     * @Assert\Email(message="install.users.email.invalid")
     * @Assert\NotBlank(message="install.users.email.not_blank")
     */
    private $email;

    /** @var UploadedFile|string */
    private $profilePicture;

    /**
     * @var string
     * @Assert\NotBlank(message="install.users.password.not_blank")
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
    public function setPassword(string $password): void
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
    public function setProfilePicture($profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    /**
     * @return string
     */
    public function getArtistName(): ?string
    {
        return $this->artistName;
    }

    /**
     * @param string $artistName
     */
    public function setArtistName(string $artistName): void
    {
        $this->artistName = $artistName;
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
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
