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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends Command
{
    /** @var CacheBuilderInterface */
    private CacheBuilderInterface $cacheBuilder;

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
            ->setName('jinya:cache:clear')
            ->setDescription('Clears the cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Clear cache for all available routes in current theme');
        $this->cacheBuilder->clearCache();
        $output->writeln('Clear cache for all routes');

        return 0;
    }
}
