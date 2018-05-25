<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 10.03.2018
 * Time: 22:39.
 */

namespace Jinya\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="api_key")
 */
class ApiKey
{
    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\User", cascade={"remove"})
     *
     * @var User
     */
    private $user;
    /**
     * @ORM\Column(type="string", name="api_key")
     * @ORM\Id
     *
     * @var string
     */
    private $key;
    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $validSince;

    /**
     * ApiKey constructor.
     */
    public function __construct()
    {
        $this->validSince = new DateTime();
    }

    /**
     * @return DateTime
     */
    public function getValidSince(): DateTime
    {
        return $this->validSince;
    }

    /**
     * @param DateTime $validSince
     */
    public function setValidSince(DateTime $validSince): void
    {
        $this->validSince = $validSince;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }
}
