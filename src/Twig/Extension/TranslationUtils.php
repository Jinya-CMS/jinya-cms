<?php

namespace Jinya\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;

class TranslationUtils extends \Twig_Extension
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * TranslationUtils constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
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
            'get_translation_catalogue' => new \Twig_Function('get_translation_catalogue', [$this, 'getTranslationCatalogue'])
        ];
    }

    public function getTranslationCatalogue()
    {
        return $this->translator->getCatalogue($this->translator->getLocale())->all('messages');
    }
}
