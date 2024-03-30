<?php

namespace Jinya\Cms\Authentication;

use Jinya\Cms\Database\Artist;

/**
 * Small helper class for the currently logged-in user
 */
class CurrentUser
{
    /** @var Artist|null The currently logged-in user */
    public static ?Artist $currentUser = null;
}
