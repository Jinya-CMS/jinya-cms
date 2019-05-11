<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 07.08.18
 * Time: 21:53
 */

namespace Jinya\Command;

use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompileCacheCommand extends Command
{
    /** @var CacheBuilderInterface */
    private $cacheBuilder;

    /**
     * CompileCacheCommand constructor.
     * @param CacheBuilderInterface $cacheBuilder
     */
    public function __construct(CacheBuilderInterface $cacheBuilder)
    {
        parent::__construct();
        $this->cacheBuilder = $cacheBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jinya:cache:compile')
            ->setDescription('Compiles the cache')
            ->addArgument('type', InputArgument::OPTIONAL)
            ->addArgument('slug', InputArgument::OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $slug = $input->getArgument('slug');
        if (empty($type) || empty($slug)) {
            $output->writeln('Compiling all available routes in current theme');
            $this->cacheBuilder->buildCache();
            $output->writeln('Compiled all routes');
        } else {
            $output->writeln(sprintf('Compiling all routes of type %s and with the slug %s', $type, $slug));
            $this->cacheBuilder->buildCacheBySlugAndType($slug, $type);
            $output->writeln(sprintf('Compiled all routes of type %s and with the slug %s', $type, $slug));
        }
    }
}
