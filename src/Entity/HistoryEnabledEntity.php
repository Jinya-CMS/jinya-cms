<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 16:29
 */

namespace Jinya\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Class HistoryEnabledEntity
 *
 * @ORM\MappedSuperclass
 */
abstract class HistoryEnabledEntity implements JsonSerializable
{
    /**
     * @var array[]
     * @ORM\Column(type="json")
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
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\User")
     */
    private $creator;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\User")
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
    public function setHistory(array $history): void
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
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
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
    public function setCreator(User $creator): void
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
     * @param DateTime $lastUpdatedAt
     */
    public function setLastUpdatedAt(DateTime $lastUpdatedAt): void
    {
        $this->lastUpdatedAt = $lastUpdatedAt;
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
    public function setUpdatedBy($updatedBy): void
    {
        if ($updatedBy instanceof User) {
            $this->updatedBy = $updatedBy;
        }
    }
}
