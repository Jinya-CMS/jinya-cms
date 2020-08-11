<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.10.2017
 * Time: 11:44.
 */

namespace Jinya\Entity\Artist;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Security\Core\User\UserInterface;
use function in_array;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements JsonSerializable, UserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_WRITER = 'ROLE_WRITER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $enabled;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private ?string $twoFactorToken = null;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Authentication\KnownDevice", mappedBy="user")
     */
    private Collection $knownDevices;

    /**
     * Encrypted password. Must be persisted.
     *
     * @ORM\Column(type="text")
     */
    private string $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     */
    private string $plainPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $confirmationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $passwordRequestedAt;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private ?array $roles = [];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private ?int $id = -1;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private ?string $firstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private ?string $lastname;

    /**
     * @ORM\Column(type="string")
     */
    private string $artistName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private ?string $profilePicture = '';

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Page\Page", mappedBy="creator")
     *
     * @var Collection
     */
    private $createdPages;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Form\Form", mappedBy="creator")
     */
    private Collection $createdForms;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private ?string $aboutMe;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->createdForms = new ArrayCollection();
        $this->createdPages = new ArrayCollection();
        $this->knownDevices = new ArrayCollection();
    }

    /**
     * @return string[]
     */
    public function getKnownDevices(): ?array
    {
        if (empty($this->knownDevices)) {
            return [];
        }

        return $this->knownDevices->getValues();
    }

    /**
     * @param string[] $knownDevices
     */
    public function setKnownDevices(array $knownDevices): void
    {
        $this->knownDevices = $knownDevices;
    }

    /**
     * @return string
     */
    public function getTwoFactorToken(): ?string
    {
        return $this->twoFactorToken;
    }

    public function setTwoFactorToken(string $twoFactorToken): void
    {
        $this->twoFactorToken = $twoFactorToken;
    }

    public function getCreatedPages(): Collection
    {
        return $this->createdPages;
    }

    public function setCreatedPages(Collection $createdPages): void
    {
        $this->createdPages = $createdPages;
    }

    public function getCreatedForms(): Collection
    {
        return $this->createdForms;
    }

    public function setCreatedForms(Collection $createdForms): void
    {
        $this->createdForms = $createdForms;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getPasswordRequestedAt(): ?DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?DateTime $passwordRequestedAt): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

    /**
     * @return array
     */
    public function getRoles(): ?array
    {
        if ($this->roles) {
            return $this->roles;
        }

        return [];
    }

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

    public function setProfilePicture(string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
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
            'artistname' => $this->artistName,
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
        return in_array($role, $this->roles, true);
    }

    public function addRole(string $role)
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role)
    {
        if (false !== ($key = array_search($role, $this->roles, true))) {
            unset($this->roles[$key]);
        }
    }

    /**
     * @return string
     */
    public function getArtistName(): ?string
    {
        return $this->artistName;
    }

    public function setArtistName(string $artistName): void
    {
        $this->artistName = $artistName;
    }

    /**
     * @return string
     */
    public function getAboutMe(): ?string
    {
        return $this->aboutMe;
    }

    public function setAboutMe(string $aboutMe): void
    {
        $this->aboutMe = $aboutMe;
    }
}
