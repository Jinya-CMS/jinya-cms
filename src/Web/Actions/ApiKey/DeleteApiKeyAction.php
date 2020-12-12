<?php

namespace App\Web\Actions\ApiKey;

use App\Database\ApiKey;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteApiKeyAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
     */
    protected function action(): Response
    {
        $device = ApiKey::findByApiKey($this->args['key']);
        if ($device === null) {
            throw new NoResultException($this->request, 'Api key not found');
        }

        $device->delete();

        return $this->noContent();
    }
}