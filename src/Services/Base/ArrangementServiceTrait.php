<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 14.04.18
 * Time: 21:34
 */

namespace Jinya\Services\Base;

trait ArrangementServiceTrait
{
    /**
     * @param array $positions
     * @param int $oldPosition
     * @param int $newPosition
     * @param $targetItem
     * @return mixed
     */
    private function rearrange(array $positions, int $oldPosition, int $newPosition, $targetItem): array
    {
        uasort($positions, static function ($a, $b) {
            /* @noinspection PhpUndefinedMethodInspection */
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        if ($oldPosition === -1 && $newPosition === -1) {
            array_splice($positions, 0, 0, [$targetItem]);
        } elseif ($oldPosition < $newPosition) {
            array_splice($positions, $newPosition + 1, 0, [$targetItem]);

            if ($oldPosition > -1) {
                array_splice($positions, $oldPosition, 1);
            }
        } else {
            array_splice($positions, $newPosition, 0, [$targetItem]);

            if ($oldPosition > -1) {
                array_splice($positions, $oldPosition + 1, 1);
            }
        }

        array_walk($positions, static function (&$item, int $index) {
            /* @noinspection PhpUndefinedMethodInspection */
            $item->setPosition($index);
        });

        return $positions;
    }
}
