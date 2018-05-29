<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.11.2017
 * Time: 00:20
 */

namespace Jinya\Components\DataTables;

use Symfony\Component\HttpFoundation\Request;

interface DatatablesRequestParserInterface
{
    /**
     * Parses a datatables.net POST request
     *
     * @param Request $request
     * @return DatatablesModel
     */
    public function parseRequest(Request $request);
}
