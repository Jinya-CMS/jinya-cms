<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\FormItem;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form/{id}/item/{position}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
class UpdateFormItemAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $formItem = FormItem::findByPosition($this->args['id'], $this->args['position']);
        if (!$formItem) {
            throw new NoResultException($this->request, 'Form item not found');
        }

        if (isset($body['label'])) {
            $formItem->label = $body['label'];
        }

        if (isset($body['placeholder'])) {
            $formItem->placeholder = $body['placeholder'];
        }

        if (isset($body['helpText'])) {
            $formItem->helpText = $body['helpText'];
        }

        if (isset($body['type'])) {
            $formItem->helpText = $body['type'];
        }

        if (isset($body['options'])) {
            $formItem->options = $body['options'];
        }

        if (isset($body['spamFilter'])) {
            $formItem->spamFilter = array_filter($body['spamFilter']);
        }

        if (isset($body['isFromAddress'])) {
            $formItem->isFromAddress = $body['isFromAddress'];
        }

        if (isset($body['isRequired'])) {
            $formItem->isRequired = $body['isRequired'];
        }

        if (isset($body['isSubject'])) {
            $formItem->isSubject = $body['isSubject'];
        }

        $formItem->update();

        if (isset($body['newPosition'])) {
            $formItem->move($body['newPosition']);
        }

        return $this->noContent();
    }
}
