<?php

namespace App\Database\Utils;

use Iterator;

class CountedResult
{
    public Iterator $items;
    public int $totalCount;
}
