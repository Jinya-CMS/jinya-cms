<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.12.2017
 * Time: 21:07
 */

namespace HelperBundle\Services\Form;


use DataBundle\Entity\Form;
use Symfony\Component\Form\FormView;

interface FormGeneratorInterface
{
    function generateForm(Form $form): FormView;
}