<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.01.2018
 * Time: 20:55.
 */

namespace Jinya\Services\Routing;

interface RouteRetrievalServiceInterface
{
    public const PAGE_DETAIL_ROUTE = 'frontend_page_details';

    public const FORM_DETAIL_ROUTE = 'frontend_form_details';

    public const ARTWORK_DETAIL_ROUTE = 'frontend_artwork_details';

    public const GALLERY_DETAIL_ROUTE = 'frontend_gallery_details';

    /**
     * Retrieves all routes for the given type.
     *
     * @param string $type
     *
     * @return array
     */
    public function retrieveRoutesByType(string $type): array;
}
