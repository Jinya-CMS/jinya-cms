<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 10.03.2018
 * Time: 22:39
 */

namespace Jinya\Entity\Authentication;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Artist\User;
use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="api_key")
 */
class ApiKey implements JsonSerializable
{
    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Artist\User", cascade={"remove"})
     */
    private User $user;

    /**
     * @ORM\Column(type="string", name="api_key")
     * @ORM\Id
     */
    private string $key;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $validSince;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $userAgent;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $remoteAddress;

    /**
     * ApiKey constructor.
     */
    public function __construct()
    {
        $this->validSince = new DateTime();
    }

    /**
     * @return string
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function getRemoteAddress(): ?string
    {
        return $this->remoteAddress;
    }

    public function setRemoteAddress(string $remoteAddress): void
    {
        $this->remoteAddress = $remoteAddress;
    }

    public function getValidSince(): DateTime
    {
        return $this->validSince;
    }

    public function setValidSince(DateTime $validSince): void
    {
        $this->validSince = $validSince;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
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
            'remoteAddress' => $this->remoteAddress,
            'userAgent' => $this->userAgent,
            'key' => $this->key,
            'validSince' => $this->validSince->format(DATE_ATOM),
        ];
    }
}
