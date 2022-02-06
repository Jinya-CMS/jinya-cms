<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
class ListAllThemesAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        if (isset($this->args['keyword'])) {
            return $this->respondList($this->formatIterator(Theme::findByKeyword($this->args['keyword'])));
        }

        return $this->respondList($this->formatIterator(Theme::findAll()));
    }
}
