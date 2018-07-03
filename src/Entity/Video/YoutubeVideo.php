<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 01.06.18
 * Time: 08:06
 */

namespace Jinya\Entity\Video;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\SlugEntity;
use Jinya\Entity\HistoryEnabledEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="youtube_video")
 */
class YoutubeVideo extends HistoryEnabledEntity
{
    use SlugEntity;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $videoKey;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Video\VideoPosition", mappedBy="youtubeVideo")
     */
    private $positions;

    /**
     * YoutubeVideo constructor.
     */
    public function __construct()
    {
        $this->positions = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    /**
     * @param Collection $positions
     */
    public function setPositions(Collection $positions): void
    {
        $this->positions = $positions;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getVideoKey(): string
    {
        return $this->videoKey;
    }

    /**
     * @param string $videoKey
     */
    public function setVideoKey(string $videoKey): void
    {
        $this->videoKey = $videoKey;
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
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'id' => $this->id,
            'videoKey' => $this->videoKey,
            'createdAt' => $this->getCreatedAt(),
            'createdBy' => $this->getCreator(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy(),
        ];
    }
}
