<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:59
 */

namespace Jinya\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="frontend_configuration")
 */
class FrontendConfiguration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="Jinya\Entity\Theme")
     * @ORM\JoinColumn(name="current_theme_id", referencedColumnName="id")
     * @var Theme
     */
    private $currentTheme;

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
     * @return Theme
     */
    public function getCurrentTheme(): Theme
    {
        return $this->currentTheme;
    }

    /**
     * @param Theme $currentTheme
     */
    public function setCurrentTheme(Theme $currentTheme): void
    {
        $this->currentTheme = $currentTheme;
    }
}