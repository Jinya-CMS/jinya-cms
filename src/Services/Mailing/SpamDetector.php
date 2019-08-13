<?php

namespace Jinya\Services\Mailing;

use Jinya\Entity\Form\Form;
use Jinya\Entity\Form\FormItem;

class SpamDetector implements SpamDetectorInterface
{
    public function checkForSpam(Form $form, array $data): bool
    {
        foreach ($form->getItems()->toArray() as $item) {
            /** @var FormItem $item */
            foreach ($item->getSpamFilter() as $keyword) {
                $position = stripos($data[lcfirst($item->getLabel())], $keyword);
                if ($position !== false && $position !== -1) {
                    return true;
                }
            }
        }

        return false;
    }
}
