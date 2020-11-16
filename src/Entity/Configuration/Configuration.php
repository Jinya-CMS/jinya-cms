<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:59
 */

namespace Jinya\Entity\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Theme\Theme;

/**
 * @ORM\Entity
 * @ORM\Table(name="configuration")
 */
class Configuration
{
    public const DEFAULT_API_KEY_INVALIDATION = 1 * 24 * 60 * 60;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\OneToOne(targetEntity="Jinya\Entity\Theme\Theme")
     * @ORM\JoinColumn(name="current_frontend_theme_id", referencedColumnName="id")
     */
    private Theme $currentTheme;

    /**
     * /**
     * @ORM\Column(type="integer")
     */
    private int $invalidateApiKeyAfter = self::DEFAULT_API_KEY_INVALIDATION;

    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private bool $messagingCenterEnabled = true;

    public function getInvalidateApiKeyAfter(): int
    {
        return $this->invalidateApiKeyAfter;
    }

    public function setInvalidateApiKeyAfter(int $invalidateApiKeyAfter): void
    {
        $this->invalidateApiKeyAfter = $invalidateApiKeyAfter;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCurrentTheme(): Theme
    {
        return $this->currentTheme;
    }

    public function setCurrentTheme(Theme $currentTheme): void
    {
        $this->currentTheme = $currentTheme;
    }

    public function isMessagingCenterEnabled(): bool
    {
        return $this->messagingCenterEnabled;
    }

    public function setMessagingCenterEnabled(bool $messagingCenterEnabled): void
    {
        $this->messagingCenterEnabled = $messagingCenterEnabled;
    }
}
