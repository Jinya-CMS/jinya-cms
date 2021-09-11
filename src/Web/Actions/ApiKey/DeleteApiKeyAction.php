<?php

namespace App\Web\Actions\ApiKey;

use App\Database\ApiKey;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/api_key/{key}', JinyaAction::DELETE)]
#[Authenticated]
class DeleteApiKeyAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $device = ApiKey::findByApiKey($this->args['key']);
        if (null === $device) {
            throw new NoResultException($this->request, 'Api key not found');
        }

        $device->delete();

        return $this->noContent();
    }
}
