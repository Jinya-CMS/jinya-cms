<?php

namespace App\Web\Actions\Form;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form/{id}', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
class GetFormByIdAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $form = Form::findById($id);
        if (null === $form) {
            throw new NoResultException($this->request, 'Form not found');
        }

        return $this->respond($form->format());
    }
}
