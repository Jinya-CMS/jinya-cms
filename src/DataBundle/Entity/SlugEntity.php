<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:36
 */

namespace DataBundle\Entity;


trait SlugEntity
{
    use BaseEntity;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $slug;

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
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }
}