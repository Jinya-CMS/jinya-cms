<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:14
 */

namespace DataBundle\Services\Theme;


use DataBundle\Entity\Theme;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use const DIRECTORY_SEPARATOR;
use function array_key_exists;
use function is_array;
use function realpath;

class ThemeService implements ThemeServiceInterface
{
    private const THEME_CONFIG_YML = 'theme.yml';
    private const JINYA_GALLERY_DEFAULT_THEME_NAME = 'jinya_gallery_default_theme';

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var string
     */
    private $themeDirectory;
    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * ThemeService constructor.
     * @param EntityManager $entityManager
     * @param string $themeDirectory
     * @param string $kernelRootDir
     */
    public function __construct(EntityManager $entityManager, string $themeDirectory, string $kernelRootDir)
    {
        $this->entityManager = $entityManager;
        $this->themeDirectory = $themeDirectory;
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * @inheritdoc
     */
    public function syncThemes(): void
    {
        $finder = new Finder();
        $themeDirectory = realpath($this->kernelRootDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $this->themeDirectory);
        $configFiles = $finder->files()
            ->in($themeDirectory)
            ->name(ThemeService::THEME_CONFIG_YML);

        foreach ($configFiles as $configFile) {
            /** @var $configFile SplFileInfo */
            $this->saveTheme($configFile->getContents(), $configFile->getRelativePath());
        }
    }

    /**
     * @param string $configString
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function saveTheme(string $configString, string $name)
    {
        $config = Yaml::parse($configString, Yaml::PARSE_OBJECT);
        $theme = $this->getThemeOrNewTheme($name);
        $theme->setName($name);
        $theme->setDisplayName($config['displayName']);
        $theme->setDescription(array_key_exists('description', $config) ? $config['description'] : '');
        if (array_key_exists('defaultConfig', $config)) {
            $themeConfig = $theme->getConfiguration();
            $defaultConfig = $config['defaultConfig'];
            if (is_array($defaultConfig)) {
                $theme->setConfiguration(array_replace_recursive($defaultConfig, $themeConfig));
            }
        }
        $theme->setPreviewImage(array_key_exists('previewImage', $config) ? $config['previewImage'] : '');

        if ($this->entityManager->getUnitOfWork()->getEntityState($theme) === UnitOfWork::STATE_NEW) {
            $this->entityManager->persist($theme);
        }

        $this->entityManager->flush();
    }

    /**
     * @param string $name
     * @return Theme
     */
    private function getThemeOrNewTheme(string $name): Theme
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
    public function saveConfig(array $config, string $themeName): void
    {
        $theme = $this->getThemeOrNewTheme($themeName);
        $theme->setConfiguration($config);
        $this->entityManager->flush();
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
        return $this->getTheme(ThemeService::JINYA_GALLERY_DEFAULT_THEME_NAME);
    }
}