<?php

namespace App\Web\Actions\KnownDevice;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\KnownDevice;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/account/known_device/{key}', JinyaAction::DELETE)]
#[JinyaAction('/api/known_device/{key}', JinyaAction::DELETE, name: 'delete_known_device_key')]
#[Authenticated]
class DeleteKnownDeviceAction extends Action
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
        $device = KnownDevice::findByCode($this->args['key']);
        if (null === $device) {
            throw new NoResultException($this->request, 'Known device not found');
        }

        $device->delete();

        return $this->noContent();
    }
}
