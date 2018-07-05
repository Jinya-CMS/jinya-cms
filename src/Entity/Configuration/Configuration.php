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
     * @var int
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Jinya\Entity\Theme\Theme")
     * @ORM\JoinColumn(name="current_frontend_theme_id", referencedColumnName="id")
     * @var \Jinya\Entity\Theme\Theme
     */
    private $currentTheme;

    /**
     * /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $invalidateApiKeyAfter = self::DEFAULT_API_KEY_INVALIDATION;

    /**
     * @return int
     */
    public function getInvalidateApiKeyAfter(): int
    {
        return $this->invalidateApiKeyAfter;
    }

    /**
     * @param int $invalidateApiKeyAfter
     */
    public function setInvalidateApiKeyAfter(int $invalidateApiKeyAfter): void
    {
        $this->invalidateApiKeyAfter = $invalidateApiKeyAfter;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \Jinya\Entity\Theme\Theme
     */
    public function getCurrentTheme(): Theme
    {
        return $this->currentTheme;
    }

    /**
     * @param \Jinya\Entity\Theme\Theme $currentTheme
     */
    public function setCurrentTheme(Theme $currentTheme): void
    {
        $this->currentTheme = $currentTheme;
    }
}
