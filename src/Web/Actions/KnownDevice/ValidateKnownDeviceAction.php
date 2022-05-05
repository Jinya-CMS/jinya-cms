<?php

namespace App\Web\Actions\KnownDevice;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\KnownDevice;
use App\Web\Actions\Action;
use App\Web\Exceptions\BadCredentialsException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

class ValidateKnownDeviceAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws BadCredentialsException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
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
