<?php

$appEnv = getenv('APP_ENV');

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Jinya\Profiling\Bundle\JinyaProfilingBundle::class => ['all' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
];

if ('dev' === $appEnv) {
    $bundles[Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class] = ['dev' => true];
    $bundles[Symfony\Bundle\DebugBundle\DebugBundle::class] = ['dev' => true];
    $bundles[Symfony\Bundle\MakerBundle\MakerBundle::class] = ['dev' => true];
    $bundles[Symfony\Bundle\WebServerBundle\WebServerBundle::class] = ['dev' => true];
} elseif ('test' === $appEnv) {
    $bundles[Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class] = ['test' => true];
    $bundles[Symfony\Bundle\DebugBundle\DebugBundle::class] = ['test' => true];
}

return $bundles;
