<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.12.2017
 * Time: 21:07
 */

namespace Jinya\Components\Form;


use Jinya\Entity\Form;
use Jinya\Entity\FormItem;
use Jinya\Services\Configuration\FrontendConfigurationServiceInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use function array_key_exists;
use function strpos;

class FormGenerator implements FormGeneratorInterface
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /** @var FrontendConfigurationServiceInterface */
    private $frontendConfigurationService;

    /**
     * FormGenerator constructor.
     * @param FormFactory $formFactory
     * @param FrontendConfigurationServiceInterface $frontendConfigurationService
     */
    public function __construct(FormFactory $formFactory, FrontendConfigurationServiceInterface $frontendConfigurationService)
    {
        $this->formFactory = $formFactory;
        $this->frontendConfigurationService = $frontendConfigurationService;
    }

    public function generateForm(Form $form): FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder();
        /** @var FormItem $item */
        foreach ($form->getItems() as $item) {
            $options = $item->getOptions();
            $options['label'] = $item->getLabel();
            if (array_key_exists('choices', $options)) {
                $choices = [];
                foreach ($options['choices'] as $choice) {
                    $choices[$choice] = $choice;
                }

                $options['choices'] = $choices;
                $activeTheme = $this->frontendConfigurationService->getConfig()->getCurrentTheme();
                $options['placeholder'] = $activeTheme->getConfiguration()['form']['dropdowns']['placeholder'];
            }

            if (strpos($item->getType(), 'TextareaType') != -1) {
                $options['attr'] = [
                    'rows' => 10
                ];
            }

            $formBuilder->add($item->getLabel(), $item->getType(), $options);
        }

        return $formBuilder->getForm();
    }
}