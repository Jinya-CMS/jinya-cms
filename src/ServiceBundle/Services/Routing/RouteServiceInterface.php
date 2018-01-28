<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.01.2018
 * Time: 21:33
 */

namespace ServiceBundle\Services\Routing;


use DataBundle\Entity\RoutingEntry;

interface RouteServiceInterface
{
    /**
     * Gets a route by its url
     *
     * @param string $url
     * @return RoutingEntry
     */
    public function findByUrl(string $url): RoutingEntry;
}