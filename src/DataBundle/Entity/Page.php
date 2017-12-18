<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:36
 */

namespace DataBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="page")
 */
class Page extends HistoryEnabledEntity
{
    use SlugEntity;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $content;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $title;

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
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
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'id' => $this->id,
            'createdAt' => $this->getCreatedAt(),
            'createdBy' => $this->getCreator(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy()
        ];
    }
}