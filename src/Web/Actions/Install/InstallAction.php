<?php

namespace App\Web\Actions\Install;

use App\Web\Actions\Action;
use JetBrains\PhpStorm\Pure;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

abstract class InstallAction extends Action
{

    protected Engine $engine;

    /**
     * InstallAction constructor.
     * @param Engine $engine
     * @param LoggerInterface $logger
     */
    #[Pure] public function __construct(Engine $engine, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->engine = $engine;
    }

    /**
     * Renders the given template with the given data
     *
     * @param string $template
     * @param array $data
     * @param int $statusCode
     * @return Response
     */
    protected function render(string $template, array $data, int $statusCode = self::HTTP_OK): Response
    {
        $this->engine->loadExtension(new URI($this->request->getUri()->getPath()));

        $this->engine->addFolder('install', __ROOT__ . '/src/Web/Templates/Installer');
        $renderResult = $this->engine->render($template, $data);
        $this->response->getBody()->write($renderResult);

        return $this->response
            ->withHeader('Content-Type', 'text/html')
            ->withStatus($statusCode);
    }
}