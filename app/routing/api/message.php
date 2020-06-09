<?php

use App\Web\Actions\Message\DeleteMessageAction;
use App\Web\Actions\Message\GetMessageAction;
use App\Web\Actions\Message\ListMessagesAction;
use App\Web\Actions\Message\MarkMessageAsNotReadAction;
use App\Web\Actions\Message\MarkMessageAsReadAction;
use App\Web\Actions\Message\MarkMessageAsSpamAction;
use App\Web\Actions\Message\MoveMessageToArchiveAction;
use App\Web\Actions\Message\MoveMessageToInboxAction;
use App\Web\Middleware\AuthenticationMiddleware;
use App\Web\Middleware\RoleMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->group('message', function (RouteCollectorProxy $group) {
        $group->get('', ListMessagesAction::class);
        $group->get('/{inbox:spam|trash|archive}', ListMessagesAction::class);
        $group->group('/{messageId}', function (RouteCollectorProxy $id) {
            $id->get('', GetMessageAction::class);
            $id->delete('', DeleteMessageAction::class);
            $id->put('/archive', MoveMessageToArchiveAction::class);
            $id->put('/spam', MarkMessageAsSpamAction::class);
            $id->put('/read', MarkMessageAsReadAction::class);
            $id->delete('/read', MarkMessageAsNotReadAction::class);
            $id->put('/inbox', MoveMessageToInboxAction::class);
        });
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
    $api->group('{formId}/message', function (RouteCollectorProxy $group) {
        $group->get('', ListMessagesAction::class);
    })->add(new RoleMiddleware(RoleMiddleware::ROLE_WRITER))->add(AuthenticationMiddleware::class);
};
