<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.12.2017
 * Time: 17:06
 */

namespace Jinya\Entity\Menu;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity
 */
class MenuItem implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\Menu", inversedBy="menuItems", cascade={"persist"})
     * @var Menu
     */
    private $menu;

    /**
     * @ORM\OneToOne(targetEntity="Jinya\Entity\Menu\RoutingEntry", mappedBy="menuItem", cascade={"persist", "remove"})
     * @var RoutingEntry
     */
    private $route;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\MenuItem", inversedBy="children", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @var MenuItem
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Menu\MenuItem", mappedBy="parent", cascade={"persist", "remove"})
     * @var Collection
     */
    private $children;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $pageType;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $highlighted = false;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $position;

    /**
     * MenuItem constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Creates a MenuItem from the given array
     *
     * @param array $item
     * @return MenuItem
     */
    public static function fromArray(array $item): self
    {
        $menuItem = new self();
        $route = RoutingEntry::fromArray($item['route']);
        $route->setMenuItem($menuItem);

        $menuItem->route = $route;
        $menuItem->highlighted = array_key_exists('highlighted', $item) ? $item['highlighted'] : false;
        $menuItem->pageType = $item['pageType'];
        $menuItem->position = $item['position'];
        $menuItem->title = $item['title'];

        return $menuItem;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return bool
     */
    public function isHighlighted(): bool
    {
        return $this->highlighted;
    }

    /**
     * @param bool $highlighted
     */
    public function setHighlighted(bool $highlighted): void
    {
        $this->highlighted = $highlighted;
    }

    /**
     * @return string
     */
    public function getPageType(): string
    {
        return $this->pageType;
    }

    /**
     * @param string $pageType
     */
    public function setPageType(string $pageType): void
    {
        $this->pageType = $pageType;
    }

    /**
     * @return Collection
     */
    public function getChildren(): ?Collection
    {
        return $this->children;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children): void
    {
        $this->children = $children;
    }

    /**
     * @return MenuItem
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param MenuItem $parent
     */
    public function setParent(?self $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return Menu|null
     */
    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    /**
     * @param Menu $menu
     */
    public function setMenu(?Menu $menu): void
    {
        $this->menu = $menu;
    }

    /**
     * @return RoutingEntry|null
     */
    public function getRoute(): ?RoutingEntry
    {
        return $this->route;
    }

    /**
     * @param RoutingEntry $route
     */
    public function setRoute(RoutingEntry $route): void
    {
        $this->route = $route;
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
        $result = [
            'id' => $this->id,
            'title' => $this->title,
            'route' => $this->route,
            'children' => $this->children->toArray(),
            'pageType' => $this->pageType,
            'displayUrl' => $this->route->getUrl(),
            'highlighted' => $this->highlighted,
        ];

        if (null !== $this->parent) {
            $result['parent'] = [
                'id' => $this->parent->getId(),
            ];
        }
        if (null !== $this->menu) {
            $result['menu'] = [
                'id' => $this->menu->getId(),
            ];
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
