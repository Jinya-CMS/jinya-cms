<?php

declare(strict_types=1);
require __DIR__ . '/../defines.php';
require __ROOT__ . '/vendor/autoload.php';

use App\Utils\AppSettingsInitializer;
use App\Web\Handlers\HttpErrorHandler;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter;

AppSettingsInitializer::loadDotEnv();

$app = AppFactory::create();

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
