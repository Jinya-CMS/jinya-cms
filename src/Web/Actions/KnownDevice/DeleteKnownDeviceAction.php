<?php

namespace App\Web\Actions\KnownDevice;

use App\Database\KnownDevice;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteKnownDeviceAction extends Action
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
        $device = KnownDevice::findByCode($this->args['key']);
        if ($device === null) {
            throw new NoResultException($this->request, 'Known device not found');
        }

        $device->delete();

        return $this->noContent();
    }
}