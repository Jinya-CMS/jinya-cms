<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\DeleteLastAdminException;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\CreatedContentException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteArtistAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     * @throws ConflictException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $artist = Artist::findById($id);
        if ($artist === null) {
            throw new NoResultException($this->request, 'Artist not found');
        }

        try {
            $artist->delete();
        } catch (InvalidQueryException $exception) {
            throw new CreatedContentException($this->request, 'Cannot delete user, has created content');
        } catch (DeleteLastAdminException $exception) {
            throw new ConflictException($this->request, 'Cannot delete last admin');
        }

        return $this->noContent();
    }
}