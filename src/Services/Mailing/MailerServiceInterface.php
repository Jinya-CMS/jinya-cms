<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.01.2018
 * Time: 19:01.
 */

namespace Jinya\Services\Mailing;

use Jinya\Entity\Form;

interface MailerServiceInterface
{
    /**
     * Formats the array and sends it.
     *
     * @param Form  $form
     * @param array $data
     */
    public function sendMail(Form $form, array $data): void;
}
