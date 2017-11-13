<?php

namespace BackendBundle\Twig\Extension;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class TranslationUtils extends \Twig_Extension
{
    /** @var Translator */
    private $translator;

    /**
     * TranslationUtils constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
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
