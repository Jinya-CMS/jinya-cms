<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:36
 */

namespace Jinya\Entity\Base;

trait SlugEntity
{
    use BaseEntity;

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private string $slug = '';

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private string $name = '';

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

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

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }
}
