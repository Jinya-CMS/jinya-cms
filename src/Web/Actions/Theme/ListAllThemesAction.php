<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllThemesAction extends ThemeAction
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(Theme::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(Theme::findAll()));
    }
}