<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Web\Handlers\HttpErrorHandler;
use App\Web\ResponseEmitter\ResponseEmitter;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (getenv('APP_ENV') === 'prod') {
    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

/** @var bool $displayErrorDetails */
$displayErrorDetails = $container->get('settings')['displayErrorDetails'];

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
