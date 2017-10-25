<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\TestInterface;

class BackendBundle extends \Codeception\Module
{
    public function _before(TestInterface $test)
    {
        parent::_before($test);
    }
}
