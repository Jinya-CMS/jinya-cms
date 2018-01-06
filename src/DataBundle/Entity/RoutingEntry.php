<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.12.2017
 * Time: 16:51
 */

namespace DataBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use function strpos;

/**
 * @ORM\Entity
 * @ORM\Table(name="route")
 * @ORM\HasLifecycleCallbacks
 */
class RoutingEntry implements JsonSerializable
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $url;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $routeName;
    /**
     * @var array
     * @ORM\Column(type="object")
     */
    private $routeParameter;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     */
    public function setRouteName(string $routeName): void
    {
        $this->routeName = $routeName;
    }

    /**
     * @return array
     */
    public function getRouteParameter(): ?array
    {
        return $this->routeParameter;
    }

    /**
     * @param array $routeParameter
     */
    public function setRouteParameter(array $routeParameter): void
    {
        $this->routeParameter = $routeParameter;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
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
            'name' => $this->routeName,
            'parameter' => $this->routeParameter,
            'url' => $this->url
        ];
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function correctUrl()
    {
        if (strpos($this->url, '/') !== 0 && strpos($this->url, 'http') !== 0 && $this->url !== '#') {
            $this->url = '/' . $this->url;
        }
    }
}