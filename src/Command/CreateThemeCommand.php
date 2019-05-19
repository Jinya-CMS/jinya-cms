<?php

namespace Jinya\Command;

use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Underscore\Types\Strings;

class CreateThemeCommand extends Command
{
    /** @var SlugServiceInterface */
    private $slugService;

    /** @var string */
    private $themesDir;

    /**
     * CreateThemeCommand constructor.
     * @param SlugServiceInterface $slugService
     * @param string $themesDir
     */
    public function __construct(SlugServiceInterface $slugService, string $themesDir)
    {
        parent::__construct();
        $this->slugService = $slugService;
        $this->themesDir = $themesDir;
    }

    protected function configure()
    {
        $this->setName('jinya:dev:theme:create');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $displayName = $helper->ask($input, $output, new Question('Display name'));
        $identifierName = $helper->ask(
            $input,
            $output,
            new Question('Identifier', $this->slugService->generateSlug($displayName))
        );
        $description = $helper->ask($input, $output, new Question('Description', ''));
        $previewImage = $helper->ask($input, $output, new Question('Preview image', ''));
        $stylesBase = $helper->ask($input, $output, new Question('SCSS style directory', 'styles'));
        $scriptsBase = $helper->ask($input, $output, new Question('JavaScript directory', 'scripts'));
        $stylesVariablesFile = $helper->ask($input, $output, new Question('SCSS variables file', '_variables.scss'));
        $stylesFiles = $helper->ask(
            $input,
            $output,
            new Question('SCSS files to compile, comma seperated', 'frontend.scss')
        );

        $config = [
            'displayName' => $displayName,
            'description' => $description,
            'previewImage' => $previewImage,
            'styles_base' => $stylesBase,
            'scripts_base' => $scriptsBase,
            'styles' => [
                'variables' => [
                    'file' => $stylesVariablesFile,
                ],
                'files' => Strings::explode(Strings::remove($stylesFiles, ' '), ','),
            ],
        ];

        $configYaml = Yaml::dump($config);
        $output->writeln("Theme $identifierName has the following config");
        $output->writeln($configYaml);

        $dumpData = $helper->ask($input, $output, new ConfirmationQuestion('Is this correct?', 'y', '/^y/i'));
        if ($dumpData) {
            $themePath = sprintf('%s/%s', $this->themesDir, $identifierName);

            $fs = new Filesystem();
            $fs->dumpFile(sprintf('%s/%s', $themePath, '/theme.yml'), $dumpData);
            $fs->touch([
                sprintf('%s/Default/index.html.twig', $themePath),
                sprintf('%s/Form/detail.html.twig', $themePath),
                sprintf('%s/Gallery/detail.html.twig', $themePath),
                sprintf('%s/Page/detail.html.twig', $themePath),
                sprintf('%s/Profile/detail.html.twig', $themePath),
                array_map(static function ($item) use ($themePath, $stylesBase) {
                    return "$themePath/$stylesBase/$item";
                }, $config['styles']['files']),
            ]);
            $fs->mkdir("$themePath/$scriptsBase");

            $addGitRepo = $helper->ask(
                $input,
                $output,
                new ConfirmationQuestion('Do you want to add a git repo?', 'n', '/^y/i')
            );

            if ($addGitRepo) {
                $repo = $helper->ask($input, $output, new Question('Git remote repo'));
                $output->writeln(passthru('git init'));
                $output->writeln(passthru("git remote add origin $repo"));
            }
        }
    }
}
