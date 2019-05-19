<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:14
 */

namespace Jinya\Services\Theme;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Jinya\Entity\Theme\Theme;
use Twig\Loader\FilesystemLoader;
use Twig_Error_Loader;

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

    /** @var string */
    private $kernelProjectDir;

    /**
     * ThemeService constructor.
     * @param EntityManagerInterface $entityManager
     * @param FilesystemLoader $twigLoader
     * @param string $themeDirectory
     * @param string $kernelProjectDir
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemLoader $twigLoader,
        string $themeDirectory,
        string $kernelProjectDir
    ) {
        $this->entityManager = $entityManager;
        $this->twigLoader = $twigLoader;
        $this->themeDirectory = $themeDirectory;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeOrNewTheme(string $name): Theme
    {
        try {
            $theme = $this->getTheme($name);
        } catch (Exception $e) {
            $theme = new Theme();
        }

        return $theme;
    }

    /**
     * {@inheritdoc}
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTheme(string $name): Theme
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('theme')
            ->from(Theme::class, 'theme')
            ->where('theme.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleResult();
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
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getDefaultJinyaTheme(): Theme
    {
        return $this->getTheme($this::JINYA_GALLERY_DEFAULT_THEME_NAME);
    }

    /**
     * {@inheritdoc}
     * @throws Twig_Error_Loader
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
        return $this->kernelProjectDir . DIRECTORY_SEPARATOR . $this->themeDirectory;
    }
}
