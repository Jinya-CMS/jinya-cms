<?php

declare(strict_types=1);
require __DIR__ . '/../defines.php';
require __ROOT__ . '/vendor/autoload.php';

use App\Web\Handlers\HttpErrorHandler;
use App\Web\ResponseEmitter\ResponseEmitter;
use App\Web\Routes\RouteResolver;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

if (file_exists(__ROOT__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__ROOT__);
    $dotenv->load();
    $dotenv->required(
        [
            'APP_ENV',
            'MYSQL_PASSWORD',
            'MYSQL_USER',
            'MYSQL_DATABASE',
            'MYSQL_HOST',
            'MAILER_HOST',
            'MAILER_PORT',
            'MAILER_SMTP_AUTH',
            'MAILER_FROM',
        ]
    );
}

$app = AppFactory::create();

// Register middleware
$middleware = require __ROOT__ . '/app/middleware.php';
$middleware($app);

$resolver = new RouteResolver();
$resolver->resolveRoutes($app);

// Register routes
$routes = require __ROOT__ . '/app/routes.php';
$routes($app);

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Add Routing Middleware
$app->addRoutingMiddleware();

// Create Error Handler
$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
$errorMiddleware = $app->addErrorMiddleware('dev' === getenv('APP_ENV'), true, 'dev' === getenv('APP_ENV'));
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
