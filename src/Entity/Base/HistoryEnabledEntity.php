<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 16:29
 */

namespace Jinya\Entity\Base;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Artist\User;
use JsonSerializable;

/**
 * Class HistoryEnabledEntity
 * @ORM\MappedSuperclass
 */
abstract class HistoryEnabledEntity implements JsonSerializable
{
    /**
     * @var array[]
     * @ORM\Column(type="json")
     */
    private array $history;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private ?DateTime $createdAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private DateTime $lastUpdatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Artist\User")
     */
    private User $creator;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Artist\User")
     */
    private ?User $updatedBy = null;

    /**
     * @return array[]
     */
    public function getHistory(): array
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

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

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
