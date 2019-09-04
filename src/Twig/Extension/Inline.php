<?php

namespace Jinya\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Inline extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'inline';
    }

    public function getFunctions()
    {
        return [
            'inline' => new TwigFunction('inline', [$this, 'inlineFile']),
        ];
    }

    public function inlineFile(string $filename): string
    {
        return file_get_contents($filename);
    }
}
