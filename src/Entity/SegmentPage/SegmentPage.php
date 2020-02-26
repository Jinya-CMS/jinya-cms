<?php

namespace Jinya\Entity\SegmentPage;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\HistoryEnabledEntity;
use Jinya\Entity\Base\SlugEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="segment_page")
 */
class SegmentPage extends HistoryEnabledEntity
{
    use SlugEntity;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\SegmentPage\Segment", mappedBy="page", cascade={"persist", "remove"})
     * @var Collection
     */
    private $segments;

    /**
     * SegmentPage constructor.
     */
    public function __construct()
    {
        $this->segments = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getSegments(): Collection
    {
        return $this->segments;
    }

    /**
     * @param Collection $segments
     */
    public function setSegments(Collection $segments): void
    {
        $this->segments = $segments;
    }

    /**
     * Specify data which should be serialized to JSON
     * @see https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'id' => $this->id,
        ];
    }
}
