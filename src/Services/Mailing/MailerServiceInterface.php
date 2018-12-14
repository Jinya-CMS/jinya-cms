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
     *
     * @param Form $form
     * @param array $data
     * @return array
     */
    public function sendMail(Form $form, array $data): array;
}
