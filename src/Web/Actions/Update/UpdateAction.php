<?php

namespace App\Web\Actions\Update;

use App\Web\Actions\Action;
use JsonException;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

abstract class UpdateAction extends Action
{

    protected Engine $engine;

    /**
     * InstallAction constructor.
     * @param Engine $engine
     * @param LoggerInterface $logger
     */
    public function __construct(Engine $engine, LoggerInterface $logger)
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

        $this->engine->addFolder('update', __ROOT__ . '/src/Web/Templates/Updater');
        $renderResult = $this->engine->render($template, $data);
        $this->response->getBody()->write($renderResult);

        return $this->response
            ->withHeader('Content-Type', 'text/html')
            ->withStatus($statusCode);
    }

    /**
     * @param string $version
     * @return string
     * @throws JsonException
     */
    protected function getReleasePath(string $version): string
    {
        $releases = $this->getReleases();
        return $releases[$version];
    }

    /**
     * @return array
     * @throws JsonException
     */
    protected function getReleases(): array
    {
        $cmsJson = json_decode(file_get_contents(getenv('JINYA_UPDATE_SERVER')), true, 512, JSON_THROW_ON_ERROR);
        return $cmsJson;
    }
}