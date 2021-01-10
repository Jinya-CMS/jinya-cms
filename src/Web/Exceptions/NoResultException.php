<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpSpecializedException;

class NoResultException extends HttpSpecializedException
{
    protected $code = Action::HTTP_NOT_FOUND;

    public function __construct(ServerRequestInterface $request, ?string $message = null)
    {
        parent::__construct($request, $message);
    }
}
