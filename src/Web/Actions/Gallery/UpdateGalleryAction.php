<?php

namespace App\Web\Actions\Gallery;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateGalleryAction extends Action
{

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws ConflictException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $slug = $this->args['slug'];
        $gallery = Gallery::findBySlug($slug);
        if ($gallery === null) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        if (isset($body['name'])) {
            $gallery->name = $body['name'];
        }

        if (isset($body['description'])) {
            $gallery->description = $body['description'];
        }

        if (isset($body['orientation'])) {
            $gallery->orientation = $body['orientation'];
        }

        if (isset($body['type'])) {
            $gallery->type = $body['type'];
        }

        try {
            $gallery->update();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->noContent();
    }
}