<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.06.18
 * Time: 18:58
 */

namespace Jinya\Entity\Galleries;

use Jinya\Entity\ArtEntityInterface;

interface GalleryInterface extends ArtEntityInterface
{
    public const HORIZONTAL = 'horizontal';

    public const VERTICAL = 'vertical';
}
