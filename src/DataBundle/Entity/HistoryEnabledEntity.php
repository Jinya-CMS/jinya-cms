<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 16:29
 */

namespace DataBundle\Entity;

use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Class HistoryEnabledEntity
 * @package DataBundle\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\MappedSuperclass
 */
abstract class HistoryEnabledEntity implements JsonSerializable
{
    /**
     * @var array[]
     * @ORM\Column(type="array")
     */
    private $history;
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $lastUpdatedAt;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="DataBundle\Entity\User", inversedBy="createdGalleries")
     */
    private $creator;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="DataBundle\Entity\User")
     */
    private $updatedBy;

    /**
     * @return array[]
     */
    public function getHistory(): ?array
    {
        return $this->history;
    }

    /**
     * @param array[] $history
     */
    function setHistory(array $history)
    {
        $this->history = $history;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return User
     */
    public function getCreator(): ?User
    {
        return $this->creator;
    }

    /**
     * @param User $creator
     */
    public function setCreator(User $creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return DateTime
     */
    public function getLastUpdatedAt(): ?DateTime
    {
        return $this->lastUpdatedAt;
    }

    /**
     * @return User
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy(User $updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * @ORM\PreUpdate
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $changeSet = $eventArgs->getEntityChangeSet();
        if (count($changeSet) > 1) {
            $this->history[] = $eventArgs->getEntityChangeSet();
        }
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->lastUpdatedAt = new DateTime();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new DateTime();
        $this->lastUpdatedAt = new DateTime();
        $historyEntry = $this->jsonSerialize();
        $historyEntry = array_map(function ($item) {
            return [null, $item];
        }, $historyEntry);
        unset($historyEntry['history']);
        $this->history[] = $historyEntry;
    }
}