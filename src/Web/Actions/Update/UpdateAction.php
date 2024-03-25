<?php

namespace App\Web\Actions\Update;

use App\Web\Actions\Action;
use JsonException;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * Base action for updates
 */
abstract class UpdateAction extends Action
{
    /** @var Engine The Plates engine */
    protected Engine $engine;

    /**
     * UpdateAction constructor.
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
     * @param array<mixed> $data
     * @param int $statusCode
     * @return Response
     * @throws Throwable
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
     * Gets the path of the given release version
     *
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
     * Gets all currently available releases from the configured update server
     *
     * @return array<string, string>
     * @throws JsonException
     */
    protected function getReleases(): array
    {
        return json_decode(
            file_get_contents(getenv('JINYA_UPDATE_SERVER') ?: '') ?: '',
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
