<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:36
 */

namespace Jinya\Entity;


trait SlugEntity
{
    use BaseEntity;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
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
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }
}