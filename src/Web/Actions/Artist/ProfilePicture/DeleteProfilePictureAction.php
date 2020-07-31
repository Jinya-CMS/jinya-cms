<?php

namespace App\Web\Actions\Artist\ProfilePicture;

use App\Database\Exceptions\EmptyResultException;
use App\Storage\ProfilePictureService;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class DeleteProfilePictureAction extends Action
{

    private ProfilePictureService $profilePictureService;

    /**
     * DeleteProfilePictureAction constructor.
     * @param LoggerInterface $logger
     * @param ProfilePictureService $profilePictureService
     */
    public function __construct(LoggerInterface $logger, ProfilePictureService $profilePictureService)
    {
        parent::__construct($logger);
        $this->profilePictureService = $profilePictureService;
    }

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        try {
            $this->profilePictureService->deleteProfilePicture($this->args['id']);
        } catch (EmptyResultException $e) {
            throw new NoResultException($this->request, 'Artist not found');
        }

        return $this->noContent();
    }
}