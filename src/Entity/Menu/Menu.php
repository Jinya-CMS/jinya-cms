<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.12.2017
 * Time: 16:51
 */

namespace Jinya\Entity\Menu;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="menu")
 */
class Menu implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Menu\MenuItem", mappedBy="menu", orphanRemoval=true, cascade={"persist", "remove"})
     * @var Collection
     */
    private $menuItems;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $logo;

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
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

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection
     */
    public function getMenuItems(): ?Collection
    {
        return $this->menuItems;
    }

    /**
     * @param Collection $menuItems
     */
    public function setMenuItems(Collection $menuItems): void
    {
        $this->menuItems = $menuItems;
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
            'name' => $this->name,
            'logo' => $this->logo,
            'children' => $this->menuItems->toArray(),
        ];
    }
}
