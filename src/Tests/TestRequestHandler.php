<?php

namespace App\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TestRequestHandler implements RequestHandlerInterface
{
    public ServerRequestInterface $request;

    public function __construct(private ResponseInterface $response)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;

        return $this->response;
    }
}