<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.11.2017
 * Time: 00:20
 */

namespace HelperBundle\Components;


use Symfony\Component\HttpFoundation\Request;

class DatatablesRequestParser implements DatatablesRequestParserInterface
{

    /**
     * @inheritdoc
     */
    public function parseRequest(Request $request): DatatablesModel
    {
        $model = new DatatablesModel();
        $model->setDraw($request->get('draw'));
        $model->setLength($request->get('length'));
        $model->setStart($request->get('start'));
        $model->setSearch($this->parseSearch($request->get('search')));
        $model->setColumns(array_map($this->parseColumn(), $request->get('columns')));
        $model->setOrder(array_map($this->parseOrder($model->getColumns()), $request->get('order')));

        return $model;
    }

    private function parseSearch(array $search): DatatablesSearchModel
    {
        $model = new DatatablesSearchModel();
        $model->setRegex($search['regex']);
        $model->setValue($search['value']);

        return $model;
    }

    private function parseColumn(): callable
    {
        return function (array $column) {
            $model = new DatatablesColumnModel();
            $model->setSearch($this->parseSearch($column['search']));
            $model->setData($column['data']);
            $model->setName($column['name']);
            $model->setOrderable($column['orderable']);
            $model->setSearchable($column['searchable']);

            return $model;
        };
    }

    private function parseOrder(array $columns): callable
    {
        return function (array $order) use ($columns) {
            $model = new DatatablesOrderModel();
            $model->setColumn($columns[$order['column']]->getData());
            $model->setDir($order['dir']);

            return $model;
        };
    }
}