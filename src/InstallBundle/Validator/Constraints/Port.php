<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 27.10.2017
 * Time: 23:10
 */

namespace InstallBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class Port
 * @package InstallBundle\Validator\Constraints
 * @Annotation
 */
class Port extends Constraint
{
    public $message = 'The port must be between 0 and 65535';
}