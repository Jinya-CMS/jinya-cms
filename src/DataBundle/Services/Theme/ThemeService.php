<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:14
 */

namespace DataBundle\Services\Theme;


use DataBundle\Entity\Theme;
use DataBundle\Services\Configuration\FrontendConfigurationServiceInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Exception;
use HelperBundle\Services\Scss\ScssCompilerServiceInterface;
use Patchwork\JSqueeze;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Twig\Loader\FilesystemLoader;
use const DIRECTORY_SEPARATOR;
use function array_key_exists;
use function file_get_contents;
use function is_array;
use function md5;
use function str_replace;
use function strcmp;

class ThemeService implements ThemeServiceInterface
{
    private const THEME_CONFIG_YML = 'theme.yml';
    private const JINYA_GALLERY_DEFAULT_THEME_NAME = 'jinya_gallery_default_theme';
    private const THEME_COMPILATION_STATE = 'theme.compilation';

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var FrontendConfigurationServiceInterface
     */
    private $frontendConfigurationService;
    /**
     * @var string
     */
    private $themeDirectory;
    /**
     * @var string
     */
    private $kernelProjectDir;
    /**
     * @var FilesystemLoader
     */
    private $twigLoader;
    /**
     * @var ScssCompilerServiceInterface
     */
    private $scssCompilerService;

    /**
     * ThemeService constructor.
     * @param EntityManager $entityManager
     * @param FrontendConfigurationServiceInterface $frontendConfigurationService
     * @param string $themeDirectory
     * @param string $kernelProjectDir
     * @param FilesystemLoader $twigLoader
     * @param ScssCompilerServiceInterface $scssCompilerService
     */
    public function __construct(EntityManager $entityManager, FrontendConfigurationServiceInterface $frontendConfigurationService, string $themeDirectory, string $kernelProjectDir, FilesystemLoader $twigLoader, ScssCompilerServiceInterface $scssCompilerService)
    {
        $this->entityManager = $entityManager;
        $this->frontendConfigurationService = $frontendConfigurationService;
        $this->themeDirectory = $themeDirectory;
        $this->kernelProjectDir = $kernelProjectDir;
        $this->twigLoader = $twigLoader;
        $this->scssCompilerService = $scssCompilerService;
    }

    /**
     * @inheritdoc
     */
    public function syncThemes(): void
    {
        $finder = new Finder();
        $themeDirectory = $this->kernelProjectDir . DIRECTORY_SEPARATOR . $this->themeDirectory;
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
     * @throws \Doctrine\ORM\ORMException
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

    /**
     * @inheritdoc
     */
    public function getThemeNamespace(Theme $theme): string
    {
        return '@Themes/' . $theme->getName();
    }

    /**
     * @inheritdoc
     */
    public function registerThemes(): void
    {
        $this->twigLoader->addPath($this->getThemeDirectory(), 'Themes');
    }

    /**
     * @inheritdoc
     */
    public function getThemeDirectory(): string
    {
        return $this->kernelProjectDir . DIRECTORY_SEPARATOR . $this->themeDirectory;
    }

    /**
     * Compiles the scss and javascript of the given @see Theme
     *
     * @param Theme $theme
     */
    public function compileTheme(Theme $theme): void
    {
        $this->compileStyles($theme);
        $this->concatScripts($theme);
    }

    /**
     * @param Theme $theme
     */
    private function compileStyles(Theme $theme): void
    {
        $themeConfig = $this->getThemeConfig($theme);
        $webStylesBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/styles/';

        $fs = new Filesystem();
        $variables = $theme->getScssVariables();

        if ($themeConfig['styles']['files']) {
            foreach ($themeConfig['styles']['files'] as $style) {
                $scssCode = $this->getScssCodeForStyle($style, $theme);
                $result = $this->scssCompilerService->compileScss($scssCode, $this->getStylesPath($theme), $variables);
                $webStylesPath = $webStylesBasePath . str_replace('scss', 'css', $style);
                $compilationCheckPath = $this->getCompilationCheckPathStyles($theme, $style);

                $fs->dumpFile($webStylesPath, $result);
                $fs->dumpFile($compilationCheckPath, md5($scssCode));
            }
        }

        $fs->dumpFile($this->getScssVariablesCompilationCheckPath($theme), implode($variables));
    }

    private function getThemeConfig(Theme $theme): array
    {
        return Yaml::parse(file_get_contents($this->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . ThemeService::THEME_CONFIG_YML));
    }

    /**
     * @param string $style
     * @param Theme $theme
     * @return string
     */
    private function getScssCodeForStyle(string $style, Theme $theme): string
    {
        $stylesPath = $this->getStylesPath($theme);
        $scssCode = file_get_contents($stylesPath . '/' . $style);

        return $scssCode;
    }

    /**
     * @param Theme $theme
     * @return string
     */
    private function getStylesPath(Theme $theme): string
    {
        $themeConfig = $this->getThemeConfig($theme);
        $stylesBasePath = 'public/scss/';
        if (array_key_exists('styles_base', $themeConfig)) {
            $stylesBasePath = $themeConfig['styles_base'];
        }

        $stylesPath = $this->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . $stylesBasePath;
        return $stylesPath;
    }

    /**
     * @param Theme $theme
     * @param string $filename
     * @return string
     */
    private function getCompilationCheckPathStyles(Theme $theme, string $filename): string
    {
        $webScriptsBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/styles/';
        $compilationCheckPath = $webScriptsBasePath . ThemeService::THEME_COMPILATION_STATE . '.' . $filename . '.' . $theme->getName();
        return $compilationCheckPath;
    }

    private function getScssVariablesCompilationCheckPath(Theme $theme)
    {
        $webStylesBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/styles/';

        return $webStylesBasePath . 'variables';
    }

    /**
     * @param Theme $theme
     */
    private function concatScripts(Theme $theme): void
    {
        $jsQueeze = new JSqueeze();
        $source = $this->getJavaScriptSource($theme);

        $code = $jsQueeze->squeeze($source);

        $webScriptsBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/scripts/';

        $fs = new Filesystem();
        $fs->dumpFile($webScriptsBasePath . 'scripts.js', $code);
        $fs->dumpFile($this->getCompilationCheckPathScripts($theme, 'scripts.js'), md5($source));
    }

    /**
     * @param Theme $theme
     * @return string
     */
    private function getJavaScriptSource(Theme $theme): string
    {
        $themeConfig = $this->getThemeConfig($theme);
        $scriptsBasePath = $this->getScriptsPath($theme);

        $source = '';

        if (!empty($themeConfig['scripts'])) {
            foreach ($themeConfig['scripts'] as $script) {
                $source .= file_get_contents($scriptsBasePath . DIRECTORY_SEPARATOR . $script) . ';';
            }
        }

        return $source;
    }

    /**
     * @param Theme $theme
     * @return string
     */
    private function getScriptsPath(Theme $theme): string
    {
        $themeConfig = $this->getThemeConfig($theme);
        $scriptsBasePath = 'public/javascripts/';
        if (array_key_exists('scripts_base', $themeConfig)) {
            $scriptsBasePath = $themeConfig['scripts_base'];
        }

        $scriptsPath = $this->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . $scriptsBasePath;
        return $scriptsPath;
    }

    /**
     * @param Theme $theme
     * @param string $filename
     * @return string
     */
    private function getCompilationCheckPathScripts(Theme $theme, string $filename): string
    {
        $webStylesBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/scripts/';
        $compilationCheckPath = $webStylesBasePath . ThemeService::THEME_COMPILATION_STATE . '.' . $filename . '.' . $theme->getName();
        return $compilationCheckPath;
    }

    /**
     * @inheritdoc
     */
    public function getActiveTheme(): Theme
    {
        return $this->frontendConfigurationService->getConfig()->getCurrentTheme();
    }

    /**
     * Checks whether the given theme is compiled
     *
     * @param Theme $theme
     * @return bool
     */
    public function isCompiled(Theme $theme): bool
    {
        return $this->isStylesCompiled($theme) && $this->isScriptsCompiled($theme);
    }

    /**
     * @param Theme $theme
     * @return bool
     */
    private function isStylesCompiled(Theme $theme): bool
    {
        $themeConfig = $this->getThemeConfig($theme);
        $fs = new Filesystem();

        $variables = $theme->getScssVariables();
        $isCompiled = $fs->exists($this->getScssVariablesCompilationCheckPath($theme)) && implode($variables) === file_get_contents($this->getScssVariablesCompilationCheckPath($theme));

        if ($themeConfig['styles']['files']) {
            foreach ($themeConfig['styles']['files'] as $style) {
                $scssCode = $this->getScssCodeForStyle($style, $theme);
                $compilationCheckPath = $this->getCompilationCheckPathStyles($theme, $style);

                $isCompiled &= $fs->exists($compilationCheckPath) && strcmp(file_get_contents($compilationCheckPath), md5($scssCode)) == 0;
            }
        }

        return $isCompiled;
    }

    /**
     * @param Theme $theme
     * @return bool
     */
    private function isScriptsCompiled(Theme $theme): bool
    {
        $fs = new Filesystem();
        $source = md5($this->getJavaScriptSource($theme));
        $scripts = $this->getCompilationCheckPathScripts($theme, 'scripts.js');
        return $fs->exists($scripts) && strcmp($source, file_get_contents($scripts)) === 0;
    }
}