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
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\Menu", inversedBy="menuItems", cascade={"persist"})
     * @var Menu
     */
    private ?Menu $menu;

    /**
     * @ORM\OneToOne(targetEntity="Jinya\Entity\Menu\RoutingEntry", mappedBy="menuItem", cascade={"persist", "remove"})
     */
    private RoutingEntry $route;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\MenuItem", inversedBy="children", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @var MenuItem
     */
    private ?MenuItem $parent;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Menu\MenuItem", mappedBy="parent", cascade={"persist", "remove"})
     */
    private Collection $children;

    /**
     * @ORM\Column(type="string")
     */
    private string $pageType;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $highlighted = false;

    /**
     * @ORM\Column(type="integer")
     */
    private int $position;

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
     * @return MenuItem
     */
    public static function fromArray(array $item): self
    {
        $menuItem = new self();
        $route = RoutingEntry::fromArray($item['route']);
        $route->setMenuItem($menuItem);

        $menuItem->route = $route;
        $menuItem->highlighted = array_key_exists('highlighted', $item) && $item['highlighted'];
        $menuItem->pageType = $item['pageType'];
        $menuItem->position = $item['position'];
        $menuItem->title = $item['title'];

        return $menuItem;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function isHighlighted(): bool
    {
        return $this->highlighted;
    }

    public function setHighlighted(bool $highlighted): void
    {
        $this->highlighted = $highlighted;
    }

    public function getPageType(): string
    {
        return $this->pageType;
    }

    public function setPageType(string $pageType): void
    {
        $this->pageType = $pageType;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

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

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

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

    public function getRoute(): ?RoutingEntry
    {
        return $this->route;
    }

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

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
