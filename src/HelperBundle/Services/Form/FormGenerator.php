<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.12.2017
 * Time: 21:07
 */

namespace HelperBundle\Services\Form;


use DataBundle\Entity\Form;
use DataBundle\Entity\FormItem;
use DataBundle\Services\Theme\ThemeServiceInterface;
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

    /**
     * @var ThemeServiceInterface
     */
    private $themeService;

    /**
     * FormService constructor.
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory, ThemeServiceInterface $themeService)
    {
        $this->formFactory = $formFactory;
        $this->themeService = $themeService;
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
                $options['placeholder'] = $this->themeService->getActiveTheme()->getConfiguration()['form']['dropdowns']['placeholder'];
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