<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:14
 */

namespace ServiceBundle\Services\Theme;


use DataBundle\Entity\Theme;
use Doctrine\ORM\EntityManager;
use Exception;
use ServiceBundle\Services\Configuration\FrontendConfigurationServiceInterface;
use Twig\Loader\FilesystemLoader;

class ThemeService implements ThemeServiceInterface
{
    const THEME_CONFIG_YML = 'theme.yml';
    const JINYA_GALLERY_DEFAULT_THEME_NAME = 'jinya_gallery_default_theme';
    const THEMES_TWIG_NAMESPACE = 'Themes';

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var FrontendConfigurationServiceInterface
     */
    private $frontendConfigurationService;
    /**
     * @var FilesystemLoader
     */
    private $twigLoader;
    /**
     * @var ThemeConfigServiceInterface
     */
    private $themeConfigService;

    /**
     * ThemeService constructor.
     * @param EntityManager $entityManager
     * @param FrontendConfigurationServiceInterface $frontendConfigurationService
     * @param string $themeDirectory
     * @param string $kernelProjectDir
     * @param FilesystemLoader $twigLoader
     * @param ThemeConfigServiceInterface $themeConfigService
     */
    public function __construct(EntityManager $entityManager, FrontendConfigurationServiceInterface $frontendConfigurationService, FilesystemLoader $twigLoader, ThemeConfigServiceInterface $themeConfigService)
    {
        $this->entityManager = $entityManager;
        $this->frontendConfigurationService = $frontendConfigurationService;
        $this->twigLoader = $twigLoader;
        $this->themeConfigService = $themeConfigService;
    }

    /**
     * @inheritdoc
     */
    function getThemeOrNewTheme(string $name): Theme
    {
        try {
            $theme = $this->getTheme($name);
        } catch (Exception $e) {
            $theme = new Theme();
        }

        return $theme;
    }

    /**
     * @inheritdoc
     */
    public function getTheme(string $name): Theme
    {
        $repository = $this->entityManager->getRepository(Theme::class);
        return $repository
            ->createQueryBuilder('theme')
            ->where('theme.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @inheritdoc
     */
    public function getAllThemes(): array
    {
        return $this->entityManager->getRepository(Theme::class)->findAll();
    }

    /**
     * @inheritdoc
     */
    public function getDefaultJinyaTheme(): Theme
    {
        return $this->getTheme($this::JINYA_GALLERY_DEFAULT_THEME_NAME);
    }

    /**
     * @inheritdoc
     */
    public function registerThemes(): void
    {
        $this->twigLoader->addPath($this->themeConfigService->getThemeDirectory(), $this::THEMES_TWIG_NAMESPACE);
    }

    /**
     * @inheritdoc
     */
    public function getActiveTheme(): Theme
    {
        return $this->frontendConfigurationService->getConfig()->getCurrentTheme();
    }
}