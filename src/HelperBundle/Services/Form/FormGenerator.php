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
use Symfony\Component\Form\FormFactory;
use function array_key_exists;
use function strpos;
use Symfony\Component\Form\FormInterface;

class FormGenerator implements FormGeneratorInterface
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * FormService constructor.
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
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