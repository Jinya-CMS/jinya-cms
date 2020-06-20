<?php

namespace App\Web\Actions\Frontend;

use App\Database;
use App\Theming;
use App\Web\Actions\Action;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Throwable;

abstract class FrontAction extends Action
{

    protected Engine $engine;

    protected Theming\Theme $activeTheme;

    /**
     * FrontAction constructor.
     * @param Engine $engine
     * @param LoggerInterface $logger
     */
    public function __construct(Engine $engine, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->engine = $engine;
        $this->activeTheme = new Theming\Theme(Database\Theme::getActiveTheme());
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        try {
            return $this->protectedAction();
        } catch (Throwable $exception) {
            if ($this->activeTheme->getErrorBehavior() === Theming\Theme::ERROR_BEHAVIOR_HOMEPAGE) {
                return $this->response
                    ->withHeader('Location', $this->request->getUri()->getHost())
                    ->withStatus(302);
            }
            if ($exception instanceof HttpException) {
                $statusCode = $exception->getCode();
            } else {
                $this->logger->error($exception->getMessage());
                $this->logger->error($exception->getTraceAsString());
                $statusCode = self::HTTP_INTERNAL_SERVER_ERROR;
            }
            try {
                return $this->render("theme::$statusCode", ['exception' => $exception], $statusCode);
            } catch (Throwable $exception) {
                $renderResult = $this->engine->render('emergency::500', ['exception' => $exception]);
                $this->response->getBody()->write($renderResult);
                $this->logger->error($exception->getMessage());
                $this->logger->error($exception->getTraceAsString());

                return $this->response
                    ->withHeader('Content-Type', 'text/html')
                    ->withStatus(self::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * Gets executed in a secure context
     *
     * @return Response
     * @throws HttpException
     */
    abstract protected function protectedAction(): Response;

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
        $parsedBody = $this->request->getParsedBody();
        $queryParams = $this->request->getQueryParams();
        $this->engine->addData(['body' => $parsedBody, 'queryParams' => $queryParams]);
        $this->engine->loadExtension($this->activeTheme);
        $this->engine->loadExtension(new Theming\MenuExtension());
        $this->engine->loadExtension(new URI($this->request->getUri()->getPath()));

        $renderResult = $this->engine
            ->render(stripos($template, 'theme::') === 0 ? $template : "theme::$template", $data);
        $this->response->getBody()->write($renderResult);

        return $this->response
            ->withHeader('Content-Type', 'text/html')
            ->withStatus($statusCode);
    }

    /**
     * @param Database\MenuItem $menuItem
     * @return Response
     */
    protected function renderMenuItem(Database\MenuItem $menuItem): Response
    {
        if ($menuItem->segmentPageId !== null) {
            $segmentPage = $menuItem->getSegmentPage();
            return $this->render('theme::segment-page', ['page' => $segmentPage]);
        }

        if ($menuItem->formId !== null) {
            $form = $menuItem->getForm();
            return $this->render('theme::form', ['form' => $form]);
        }

        if ($menuItem->artistId !== null) {
            $artist = $menuItem->getArtist();
            return $this->render('theme::profile', ['artist' => $artist]);
        }

        if ($menuItem->galleryId !== null) {
            $gallery = $menuItem->getGallery();
            return $this->render('theme::gallery', ['gallery' => $gallery]);
        }

        if ($menuItem->pageId !== null) {
            $page = $menuItem->getPage();
            return $this->render('theme::simple-page', ['page' => $page]);
        }

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}