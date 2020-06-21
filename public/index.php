<?php
declare(strict_types=1);
require __DIR__ . '/../defines.php';
require __ROOT__ . '/vendor/autoload.php';

use App\Web\Handlers\HttpErrorHandler;
use App\Web\ResponseEmitter\ResponseEmitter;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

if (file_exists(__ROOT__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__ROOT__);
    $dotenv->load();
}

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (getenv('APP_ENV') === 'prod') {
    $containerBuilder->enableCompilation(__ROOT__ . '/var/cache');
}

// Set up settings
$settings = require __ROOT__ . '/app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __ROOT__ . '/app/dependencies.php';
$dependencies($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register middleware
$middleware = require __ROOT__ . '/app/middleware.php';
$middleware($app);

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
$errorMiddleware = $app->addErrorMiddleware(getenv('APP_ENV') === 'dev', true, getenv('APP_ENV') === 'dev');
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
