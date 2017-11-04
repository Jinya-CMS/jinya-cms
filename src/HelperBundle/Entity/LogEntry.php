<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 15:32
 */

namespace HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="log")
 * @ORM\HasLifecycleCallbacks
 */
class LogEntry implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(name="message", type="text")
     * @var string
     */
    private $message;

    /**
     * @ORM\Column(name="context", type="array")
     * @var array
     */
    private $context;

    /**
     * @ORM\Column(name="level", type="smallint")
     * @var int
     */
    private $level;

    /**
     * @ORM\Column(name="level_name", type="string", length=50)
     * @var string
     */
    private $levelName;

    /**
     * @ORM\Column(name="extra", type="array")
     * @var array
     */
    private $extra;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level)
    {
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * @param string $levelName
     */
    public function setLevelName(string $levelName)
    {
        $this->levelName = $levelName;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     */
    public function setExtra(array $extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
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
            'createdAt' => $this->createdAt,
            'message' => $this->message,
            'level' => $this->levelName,
            'extra' => $this->extra,
            'context' => $this->context,
            'id' => $this->id
        ];
    }
}