<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 15.07.18
 * Time: 16:45
 */

namespace Jinya\Entity\Authentication;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Artist\User;
use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="known_device")
 */
class KnownDevice implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", name="device_key")
     */
    private string $key;

    /**
     * @ORM\Column(type="string")
     */
    private string $userAgent;

    /**
     * @ORM\Column(type="string")
     */
    private string $remoteAddress;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Artist\User", inversedBy="knownDevices")
     */
    private User $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function getRemoteAddress(): string
    {
        return $this->remoteAddress;
    }

    public function setRemoteAddress(string $remoteAddress): void
    {
        $this->remoteAddress = $remoteAddress;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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
        ];
    }
}
