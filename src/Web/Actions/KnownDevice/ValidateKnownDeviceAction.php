<?php

namespace App\Web\Actions\KnownDevice;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\KnownDevice;
use App\Web\Actions\Action;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\BadCredentialsException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/known_device/{key}', JinyaAction::HEAD)]
class ValidateKnownDeviceAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws BadCredentialsException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $key = $this->args['key'];
        if (KnownDevice::findByCode($key)) {
            return $this->noContent();
        }

        throw new BadCredentialsException($this->request, 'Known device is unknown');
    }
}
