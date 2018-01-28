<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 25.10.2017
 * Time: 19:24.
 */

namespace HelperBundle\Components\Database;

interface SchemaToolInterface
{
    /**
     * Updates the database schema
     */
    public function updateSchema(): void;
}
