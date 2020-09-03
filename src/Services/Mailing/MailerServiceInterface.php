<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.01.2018
 * Time: 19:01
 */

namespace Jinya\Services\Mailing;

use Jinya\Entity\Form\Form;

interface MailerServiceInterface
{
    /**
     * Formats the array and sends it
     */
    public function sendMail(Form $form, array $data): array;

    /**
     * Gets the body for the given form and data
     */
    public function getBody(Form $form, array $data): string;
}
