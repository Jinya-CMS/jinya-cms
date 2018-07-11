<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.10.2017
 * Time: 11:44.
 */

namespace Jinya\Entity\Artist;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Security\Core\User\UserInterface;
use function in_array;
use function uniqid;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements JsonSerializable, UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const ROLE_WRITER = 'ROLE_WRITER';

    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * The salt to use for hashing.
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     * @ORM\Column(type="text")
     */
    private $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    private $plainPassword;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private $profilePicture;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Artwork\Artwork", mappedBy="creator")
     *
     * @var Collection
     */
    private $createdArtworks;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Gallery\ArtGallery", mappedBy="creator")
     *
     * @var Collection
     */
    private $createdArtGalleries;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Page\Page", mappedBy="creator")
     *
     * @var Collection
     */
    private $createdPages;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Form\Form", mappedBy="creator")
     *
     * @var Collection
     */
    private $createdForms;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->roles = [];
        $this->salt = uniqid();
        $this->createdArtworks = new ArrayCollection();
        $this->createdForms = new ArrayCollection();
        $this->createdArtGalleries = new ArrayCollection();
        $this->createdPages = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getCreatedArtworks(): Collection
    {
        return $this->createdArtworks;
    }

    /**
     * @param Collection $createdArtworks
     */
    public function setCreatedArtworks(Collection $createdArtworks): void
    {
        $this->createdArtworks = $createdArtworks;
    }

    /**
     * @return Collection
     */
    public function getCreatedArtGalleries(): Collection
    {
        return $this->createdArtGalleries;
    }

    /**
     * @param Collection $createdArtGalleries
     */
    public function setCreatedArtGalleries(Collection $createdArtGalleries): void
    {
        $this->createdArtGalleries = $createdArtGalleries;
    }

    /**
     * @return Collection
     */
    public function getCreatedPages(): Collection
    {
        return $this->createdPages;
    }

    /**
     * @param Collection $createdPages
     */
    public function setCreatedPages(Collection $createdPages): void
    {
        $this->createdPages = $createdPages;
    }

    /**
     * @return Collection
     */
    public function getCreatedForms(): Collection
    {
        return $this->createdForms;
    }

    /**
     * @param Collection $createdForms
     */
    public function setCreatedForms(Collection $createdForms): void
    {
        $this->createdForms = $createdForms;
    }

    /**
     * @return string
     */
    public function getEmail(): string
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

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @return string
     */
    public function getPassword(): string
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
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime|null $lastLogin
     */
    public function setLastLogin(?\DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return null|string
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param null|string $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return \DateTime|null
     */
    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    /**
     * @param \DateTime|null $passwordRequestedAt
     */
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    /**
     * @param string $profilePicture
     */
    public function setProfilePicture(string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'profilepicture' => $this->profilePicture,
            'email' => $this->email,
            'roles' => $this->roles,
        ];
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = '';
    }

    public function hasRole(string $role)
    {
        return in_array($role, $this->roles);
    }

    public function addRole(string $role)
    {
        if (false === (array_search($role, $this->roles))) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role)
    {
        if (false !== ($key = array_search($role, $this->roles))) {
            unset($this->roles[$key]);
        }
    }
}
