<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.10.2017
 * Time: 11:44.
 */

namespace DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser implements JsonSerializable
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_WRITER = 'ROLE_WRITER';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

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
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $profilePicture;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="DataBundle\Entity\Gallery", mappedBy="creator")
     */
    private $createdGalleries;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->createdGalleries = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getCreatedGalleries(): ArrayCollection
    {
        return $this->createdGalleries;
    }

    /**
     * @param ArrayCollection $createdGalleries
     */
    public function setCreatedGalleries(ArrayCollection $createdGalleries): void
    {
        $this->createdGalleries = $createdGalleries;
    }

    /**
     * @return string
     */
    public function getProfilePicture(): string
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
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
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
            'username' => $this->username,
            'roles' => $this->roles
        ];
    }
}
