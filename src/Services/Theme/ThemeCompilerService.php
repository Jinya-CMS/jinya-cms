<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 07:55
 */

namespace Jinya\Services\Theme;

use Jinya\Entity\Theme\Theme;
use Jinya\Services\Scss\ScssCompilerServiceInterface;
use Patchwork\JSqueeze;
use Symfony\Component\Filesystem\Filesystem;
use const DIRECTORY_SEPARATOR;

class ThemeCompilerService implements ThemeCompilerServiceInterface
{
    private const THEME_COMPILATION_STATE = 'theme.compilation';

    /** @var ThemeConfigServiceInterface */
    private ThemeConfigServiceInterface $themeConfigService;

    /** @var ThemeServiceInterface */
    private ThemeServiceInterface $themeService;

    /** @var ScssCompilerServiceInterface */
    private ScssCompilerServiceInterface $scssCompilerService;

    /** @var string */
    private string $kernelProjectDir;

    /**
     * ThemeCompilerService constructor.
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param ScssCompilerServiceInterface $scssCompilerService
     * @param string $kernelProjectDir
     */
    public function __construct(
        ThemeConfigServiceInterface $themeConfigService,
        ThemeServiceInterface $themeService,
        ScssCompilerServiceInterface $scssCompilerService,
        string $kernelProjectDir
    ) {
        $this->themeConfigService = $themeConfigService;
        $this->themeService = $themeService;
        $this->scssCompilerService = $scssCompilerService;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * Compiles the scss and javascript of the given @param Theme $theme
     * @see \Jinya\Entity\Theme\Theme
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
        $themeConfig = $this->themeConfigService->getThemeConfig($theme->getName());
        $webStylesBasePath = $this->getTargetBasePath($theme) . '/styles/';

        $fs = new Filesystem();
        $variables = $theme->getScssVariables();

        if ($themeConfig['styles']['files']) {
            foreach ($themeConfig['styles']['files'] as $style) {
                $scssCode = $this->getScssCodeForStyle($style, $theme);
                $result = $this->scssCompilerService->compileScss(
                    $scssCode,
                    $this->themeConfigService->getStylesPath($theme),
                    $variables
                );
                $webStylesPath = $webStylesBasePath . str_replace('scss', 'css', basename($style));
                $compilationCheckPath = $this->getCompilationCheckPathStyles($theme, $style);

                $fs->dumpFile($webStylesPath, $result);
                $fs->dumpFile($compilationCheckPath, md5($scssCode));
            }
        }

        $fs->dumpFile($this->getScssVariablesCompilationCheckPath($theme), implode($variables));
    }

    /**
     * @param Theme $theme
     * @return string
     */
    private function getTargetBasePath(Theme $theme): string
    {
        return $this->kernelProjectDir . '/public/public/' . $theme->getName();
    }

    /**
     * @param string $style
     * @param Theme $theme
     * @return string
     */
    private function getScssCodeForStyle(string $style, Theme $theme): string
    {
        $stylesPath = $this->themeConfigService->getStylesPath($theme);

        return file_get_contents($stylesPath . '/' . $style);
    }

    /**
     * @param Theme $theme
     * @param string $filename
     * @return string
     */
    private function getCompilationCheckPathStyles(Theme $theme, string $filename): string
    {
        $webStylesBasePath = $this->getTargetBasePath($theme) . '/styles/';

        return $webStylesBasePath . self::THEME_COMPILATION_STATE . '.' . $filename . '.' . $theme->getName();
    }

    private function getScssVariablesCompilationCheckPath(Theme $theme): string
    {
        $webStylesBasePath = $this->getTargetBasePath($theme) . '/styles/';

        return $webStylesBasePath . 'variables';
    }

    private function concatScripts(Theme $theme): void
    {
        $fs = new Filesystem();
        $themeConfig = $this->themeConfigService->getThemeConfig($theme->getName());
        $scriptsBasePath = $this->getScriptsPath($theme);
        $webStylesBasePath = $this->getTargetBasePath($theme) . '/scripts/';
        $jsQueeze = new JSqueeze();

        if ($themeConfig['scripts']['files']) {
            foreach ($themeConfig['scripts']['files'] as $key => $scripts) {
                $source = $this->getJavaScriptSource($scriptsBasePath, $scripts);
                $compilationCheckPath = $this->getCompilationCheckPathScripts($theme, $key);
                $compiled = $source;

                if ('!' !== substr($key, -strlen('!'))) {
                    if ('yes' !== getenv('APP_DEBUG')) {
                        $compiled = $jsQueeze->squeeze($source);
                    }
                } else {
                    $key = trim(str_replace('!', null, $key));
                }

                $targetPath = $webStylesBasePath . $key;

                $fs->dumpFile($targetPath, $compiled);
                $fs->dumpFile($compilationCheckPath, md5($source));
            }
        }
    }

    /**
     * @param Theme $theme
     * @return string
     */
    private function getScriptsPath(Theme $theme): string
    {
        $themeConfig = $this->themeConfigService->getThemeConfig($theme->getName());
        $scriptsBasePath = 'public/javascripts/';
        if (array_key_exists('scripts_base', $themeConfig)) {
            $scriptsBasePath = $themeConfig['scripts_base'];
        }

        return implode(
            DIRECTORY_SEPARATOR,
            [$this->themeService->getThemeDirectory(), $theme->getName(), $scriptsBasePath]
        );
    }

    /**
     * @param string $scriptsBasePath
     * @param array $scripts
     * @return string
     */
    private function getJavaScriptSource(string $scriptsBasePath, array $scripts): string
    {
        $source = '';

        if (!empty($scripts)) {
            foreach ($scripts as $script) {
                $source .= file_get_contents($scriptsBasePath . DIRECTORY_SEPARATOR . $script) . "\n";
            }
        }

        return $source;
    }

    /**
     * @param Theme $theme
     * @param string $filename
     * @return string
     */
    private function getCompilationCheckPathScripts(Theme $theme, string $filename): string
    {
        $webStylesBasePath = $this->getTargetBasePath($theme) . '/scripts/';

        return $webStylesBasePath . self::THEME_COMPILATION_STATE . '.' . $filename . '.' . $theme->getName();
    }

    /**
     * {@inheritdoc}
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
        $themeConfig = $this->themeConfigService->getThemeConfig($theme->getName());
        $fs = new Filesystem();

        $variables = $theme->getScssVariables();
        $isCompiled = $fs->exists(
            $this->getScssVariablesCompilationCheckPath($theme)
        ) && implode($variables) === file_get_contents($this->getScssVariablesCompilationCheckPath($theme));

        if ($themeConfig['styles']['files']) {
            foreach ($themeConfig['styles']['files'] as $style) {
                $scssCode = $this->getScssCodeForStyle($style, $theme);
                $compilationCheckPath = $this->getCompilationCheckPathStyles($theme, $style);

                $isCompiled &= $fs->exists($compilationCheckPath)
                    && 0 === strcmp(
                        file_get_contents($compilationCheckPath),
                        md5($scssCode)
                    );
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
        $themeConfig = $this->themeConfigService->getThemeConfig($theme->getName());

        $isCompiled = true;
        $scriptsBasePath = $this->getScriptsPath($theme);

        if ($themeConfig['scripts']['files']) {
            foreach ($themeConfig['scripts']['files'] as $key => $scripts) {
                $source = $this->getJavaScriptSource($scriptsBasePath, $scripts);
                $compilationCheckPath = $this->getCompilationCheckPathScripts($theme, $key);

                $isCompiled &= $fs->exists($compilationCheckPath)
                    && 0 === strcmp(
                        file_get_contents($compilationCheckPath),
                        md5($source)
                    );
            }
        }

        return $isCompiled;
    }
}
