<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.12.2017
 * Time: 21:07
 */

namespace HelperBundle\Services\Form;


use DataBundle\Entity\Form;
use Symfony\Component\Form\FormInterface;

interface FormGeneratorInterface
{
    public function generateForm(Form $form): FormInterface;
}