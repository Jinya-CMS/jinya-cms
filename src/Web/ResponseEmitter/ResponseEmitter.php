<?php

declare(strict_types=1);

namespace App\Web\ResponseEmitter;

use Psr\Http\Message\ResponseInterface;
use Slim\ResponseEmitter as SlimResponseEmitter;

/**
 *
 */
class ResponseEmitter extends SlimResponseEmitter
{
    /**
     * @inheritDoc
     */
    public function emit(ResponseInterface $response): void
    {
        $newResponse = $response
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
            ->withHeader('Pragma', 'no-cache');

        if (ob_get_contents()) {
            ob_clean();
        }

        parent::emit($newResponse);
    }
}
