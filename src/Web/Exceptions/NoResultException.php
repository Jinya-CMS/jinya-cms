<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpSpecializedException;

/**
 * This exception is thrown, when the requested entity is not found in the database
 */
class NoResultException extends HttpSpecializedException
{
    /** @var int The return code for missing results, currently 404 */
    protected $code = Action::HTTP_NOT_FOUND;

    /**
     * Creates a new NoResultException
     *
     * @param ServerRequestInterface $request
     * @param string|null $message
     */
    public function __construct(ServerRequestInterface $request, ?string $message = null)
    {
        parent::__construct($request, $message);
    }
}
