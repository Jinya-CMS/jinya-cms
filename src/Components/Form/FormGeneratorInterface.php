<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.12.2017
 * Time: 21:07
 */

namespace Jinya\Components\Form;

use Jinya\Entity\Form\Form;
use Symfony\Component\Form\FormInterface;

interface FormGeneratorInterface
{
    /**
     * Generates a @param Form $form
     * @see FormInterface based on the given @see Form
     */
    public function generateForm(Form $form): FormInterface;
}
