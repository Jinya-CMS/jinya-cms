<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:14
 */

namespace Jinya\Services\Theme;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Theme\Theme;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

class ThemeService implements ThemeServiceInterface
{
    public const THEME_CONFIG_YML = 'theme.yml';

    public const JINYA_GALLERY_DEFAULT_THEME_NAME = 'jinya-default-theme';

    public const THEMES_TWIG_NAMESPACE = 'Theme';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var FilesystemLoader */
    private $twigLoader;

    /** @var string */
    private $themeDirectory;

    /**
     * ThemeService constructor.
     * @param EntityManagerInterface $entityManager
     * @param FilesystemLoader $twigLoader
     * @param string $themeDirectory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemLoader $twigLoader,
        string $themeDirectory
    ) {
        $this->entityManager = $entityManager;
        $this->twigLoader = $twigLoader;
        $this->themeDirectory = $themeDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeOrNewTheme(string $name): Theme
    {
        $theme = $this->getTheme($name);

        if ($theme === null) {
            $theme = new Theme();
        }

        return $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme(string $name): Theme
    {
        return $this->entityManager
            ->getRepository(Theme::class)
            ->findOneBy(['name' => $name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllThemes(): array
    {
        return $this->entityManager->getRepository(Theme::class)->findAll();
    }

    /**
     * {@inheritdoc}
     * @return Theme
     */
    public function getDefaultJinyaTheme(): Theme
    {
        return $this->getTheme($this::JINYA_GALLERY_DEFAULT_THEME_NAME);
    }

    /**
     * {@inheritdoc}
     * @throws LoaderError
     */
    public function registerThemes(): void
    {
        $this->twigLoader->addPath($this->getThemeDirectory(), $this::THEMES_TWIG_NAMESPACE);
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeDirectory(): string
    {
        return $this->themeDirectory;
    }

    /**
     * Updates the theme
     *
     * @param Theme $theme
     */
    public function update(Theme $theme): void
    {
        $this->entityManager->flush();
    }
}
