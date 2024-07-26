<?php

namespace Jinya\Cms\Web\Controllers;

use Iterator;
use Jinya\Router\Http\AbstractController;
use JsonException;
use Psr\Http\Message\ResponseInterface;

/**
 * @property array<string, mixed> $body
 */
abstract class BaseController extends AbstractController
{
    public const DEVICE_CODE_COOKIE = 'JinyaDeviceCode';

    /**
     * @throws JsonException
     */
    public function jsonIteratorPlain(Iterator $iterator): ResponseInterface
    {
        return $this->json($this->formatIteratorPlain($iterator));
    }

    /**
     * @template T
     * @param Iterator<T> $iterator
     * @return array<array<string, mixed>>
     * @throws JsonException
     */
    public function formatIteratorPlain(Iterator $iterator): array
    {
        return array_map(static fn (mixed $item) => $item->format(), iterator_to_array($iterator));
    }

    /**
     * @throws JsonException
     */
    public function jsonIterator(Iterator $iterator): ResponseInterface
    {
        $items = array_map(static fn (mixed $item) => $item->format(), iterator_to_array($iterator));

        return $this->json([
            'offset' => 0,
            'itemsCount' => count($items),
            'totalCount' => count($items),
            'items' => $items,
        ]);
    }

    /**
     * @throws JsonException
     */
    public function entityNotFound(string $message): ResponseInterface
    {
        return $this->notFound([
            'success' => false,
            'error' => [
                'message' => $message,
                'type' => 'not-found',
            ],
        ]);
    }
}
