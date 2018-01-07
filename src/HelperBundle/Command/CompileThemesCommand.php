<?php

namespace HelperBundle\Command;

use DataBundle\Services\Theme\ThemeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompileThemesCommand extends ContainerAwareCommand
{
    /** @var ThemeServiceInterface */
    private $themeService;

    /**
     * CompileThemesCommand constructor.
     * @param ThemeServiceInterface $themeService
     */
    public function __construct(ThemeServiceInterface $themeService)
    {
        parent::__construct();
        $this->themeService = $themeService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jinya:themes:compile')
            ->setDescription('Compiles all themes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $themes = $this->themeService->getAllThemes();
        foreach ($themes as $theme) {
            $output->writeln('Compiling theme ' . $theme->getName());
            $this->themeService->compileTheme($theme);
            $output->writeln('Compiled theme ' . $theme->getName());
        }

        $output->writeln('Compiled all themes');
    }
}
