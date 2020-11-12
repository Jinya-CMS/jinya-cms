<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.12.2017
 * Time: 16:51
 */

namespace Jinya\Entity\Menu;

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
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $url;

    /**
     * @ORM\Column(type="string")
     */
    private string $routeName = '';

    /**
     * @ORM\Column(type="object")
     */
    private array $routeParameter = [];

    /**
     * @ORM\OneToOne(targetEntity="Jinya\Entity\Menu\MenuItem", inversedBy="route")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private MenuItem $menuItem;

    /**
     * Creates a RoutingEntry from the given array
     *
     * @param $route
     * @return RoutingEntry
     */
    public static function fromArray($route): self
    {
        $routingEntry = new self();
        $routingEntry->routeName = $route['name'];
        $routingEntry->routeParameter = $route['parameter'];
        $routingEntry->url = $route['url'];

        return $routingEntry;
    }

    public function getMenuItem(): MenuItem
    {
        return $this->menuItem;
    }

    public function setMenuItem(MenuItem $menuItem): void
    {
        $this->menuItem = $menuItem;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

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

    public function setId(string $id): void
    {
        $this->id = $id;
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
            'name' => $this->routeName,
            'parameter' => $this->routeParameter,
            'url' => $this->url,
        ];
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function correctUrl(): void
    {
        if ('' !== $this->url && 0 !== strpos($this->url, '/') && 0 !== strpos($this->url, 'http')) {
            $this->url = '/' . $this->url;
        }
    }
}
