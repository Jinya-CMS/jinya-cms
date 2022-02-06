<?php

namespace App\Web\Actions\Install;

use App\Web\Actions\Action;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

abstract class InstallAction extends Action
{

    protected Engine $engine;

    /**
     * InstallAction constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->engine = \App\Theming\Engine::getPlatesEngine();
    }

    /**
     * Renders the given template with the given data
     *
     * @param string $template
     * @param array $data
     * @param int $statusCode
     * @return Response
     * @throws Throwable
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