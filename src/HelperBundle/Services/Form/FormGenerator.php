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
use Symfony\Component\Form\FormView;

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

    function generateForm(Form $form): FormView
    {
        $formBuilder = $this->formFactory->createBuilder();
        /** @var FormItem $item */
        foreach ($form->getItems() as $item) {
            $options = $item->getOptions();
            $options['label'] = $item->getLabel();
            $formBuilder->add($item->getLabel(), $item->getType(), $options);
        }

        return $formBuilder->getForm()->createView();
    }
}