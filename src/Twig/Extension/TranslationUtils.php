<?php

namespace Jinya\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Yaml\Yaml;
use const DIRECTORY_SEPARATOR;

class TranslationUtils extends \Twig_Extension
{
    /** @var string */
    private $kernelProjectDir;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * TranslationUtils constructor.
     *
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
    public function getName()
    {
        return 'translation_utils';
    }

    public function getFunctions()
    {
        return [
            'get_translation_catalogue' => new \Twig_Function('get_translation_catalogue', [$this, 'getTranslationCatalogue']),
        ];
    }

    public function getTranslationCatalogue(string $catalogue = 'messages')
    {
        $locale = $this->translator->getLocale();

        $path = $this->kernelProjectDir.'/translations';

        return Yaml::parseFile($path.DIRECTORY_SEPARATOR.$catalogue.'.'.$locale.'.yml');
    }
}
