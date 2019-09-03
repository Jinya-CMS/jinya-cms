<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 20:16
 */

namespace Jinya\Twig\Extension;

use Doctrine\Common\Collections\Collection;
use Jinya\Entity\Menu\MenuItem;
use Twig_Extension;
use Twig_SimpleFilter;

class SortByPosition extends Twig_Extension
{
    public function getFilters()
    {
        return [
            'sortByPosition' => new Twig_SimpleFilter('sortByPosition', [$this, 'sortByPosition']),
        ];
    }

    public function sortByPosition($items)
    {
        if ($items instanceof Collection) {
            $elements = $items->toArray();
        } else {
            $elements = $items;
        }

        usort($elements, static function ($item1, $item2) {
            /** @var MenuItem $item1 */
            /** @var MenuItem $item2 */
            if ($item1->getPosition() === $item2->getPosition()) {
                return 0;
            }

            return $item1->getPosition() < $item2->getPosition() ? -1 : 1;
        });

        return $elements;
    }
}
