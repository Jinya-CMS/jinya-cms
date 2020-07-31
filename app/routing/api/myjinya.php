<?php

use App\Web\Actions\MyJinya\GetArtistInfoAction;
use App\Web\Actions\MyJinya\UpdateAboutMeAction;
use App\Web\Actions\MyJinya\UpdateProfilePictureAction;
use App\Web\Middleware\AuthenticationMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $api) {
    $api->get('account', GetArtistInfoAction::class)->add(AuthenticationMiddleware::class);
    $api->get('me', GetArtistInfoAction::class)->add(AuthenticationMiddleware::class);
    $api->put('me', UpdateAboutMeAction::class)->add(AuthenticationMiddleware::class);
    $api->put('me/profilepicture', UpdateProfilePictureAction::class)->add(AuthenticationMiddleware::class);
};