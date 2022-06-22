<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\FormItem;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to update form items
 */
class UpdateFormItemAction extends Action
{
    /**
     * Updates the form item by form id and position
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $formItem = FormItem::findByPosition($this->args['id'], $this->args['position']);
        if (!$formItem) {
            throw new NoResultException($this->request, 'Form item not found');
        }

        if (isset($this->body['label'])) {
            $formItem->label = $this->body['label'];
        }

        if (isset($this->body['placeholder'])) {
            $formItem->placeholder = $this->body['placeholder'];
        }

        if (isset($this->body['helpText'])) {
            $formItem->helpText = $this->body['helpText'];
        }

        if (isset($this->body['type'])) {
            $formItem->helpText = $this->body['type'];
        }

        if (isset($this->body['options'])) {
            $formItem->options = $this->body['options'];
        }

        if (isset($this->body['spamFilter'])) {
            $formItem->spamFilter = array_filter($this->body['spamFilter']);
        }

        if (isset($this->body['isFromAddress'])) {
            $formItem->isFromAddress = $this->body['isFromAddress'];
        }

        if (isset($this->body['isRequired'])) {
            $formItem->isRequired = $this->body['isRequired'];
        }

        if (isset($this->body['isSubject'])) {
            $formItem->isSubject = $this->body['isSubject'];
        }

        $formItem->update();

        if (isset($this->body['newPosition'])) {
            $formItem->move($this->body['newPosition']);
        }

        return $this->noContent();
    }
}
