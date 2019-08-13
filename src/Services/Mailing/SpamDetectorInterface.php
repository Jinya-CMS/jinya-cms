<?php


namespace Jinya\Services\Mailing;

use Jinya\Entity\Form\Form;

interface SpamDetectorInterface
{
    public function checkForSpam(Form $form, array $data): bool;
}
