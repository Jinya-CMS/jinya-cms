<?php

namespace Jinya\Twig\Extension;

use Twig_Extension;
use Twig_Function;
use function file_get_contents;

class Inline extends Twig_Extension
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
            'inline' => new Twig_Function('inline', [$this, 'inlineFile']),
        ];
    }

    public function inlineFile(string $filename): string
    {
        return file_get_contents($filename);
    }
}
