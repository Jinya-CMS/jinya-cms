<?php

namespace Jinya\Twig\Extension;

use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TranslationUtils extends AbstractExtension
{
    /** @var string */
    private string $kernelProjectDir;

    /** @var TranslatorInterface */
    private TranslatorInterface $translator;

    /**
     * TranslationUtils constructor.
     * @param string $kernelProjectDir
     * @param TranslatorInterface $translator
     */
    public function __construct(string $kernelProjectDir, TranslatorInterface $translator)
    {
        $this->kernelProjectDir = $kernelProjectDir;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'translation_utils';
    }

    public function getFunctions()
    {
        return [
            'get_translation_catalogue' => new TwigFunction(
                'get_translation_catalogue',
                [$this, 'getTranslationCatalogue']
            ),
        ];
    }

    public function getTranslationCatalogue(string $catalogue = 'messages')
    {
        $locale = $this->translator->getLocale();

        $path = $this->kernelProjectDir . '/translations';

        return Yaml::parseFile($path . DIRECTORY_SEPARATOR . $catalogue . '.' . $locale . '.yml');
    }
}
