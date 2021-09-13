<?php

namespace App\Web\Actions\Frontend;

use App\Database;
use App\Theming;
use App\Web\Actions\Action;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpException;
use Throwable;

abstract class FrontAction extends Action
{
    protected Engine $engine;

    protected Theming\Theme $activeTheme;

    /**
     * FrontAction constructor.
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     */
    public function __construct()
    {
        parent::__construct();
        $this->engine = Theming\Engine::getPlatesEngine();
        $this->activeTheme = new Theming\Theme(Database\Theme::getActiveTheme());
    }

    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        try {
            return $this->protectedAction();
        } catch (Throwable $exception) {
            if (Theming\Theme::ERROR_BEHAVIOR_HOMEPAGE === $this->activeTheme->getErrorBehavior()) {
                $this->logger->error($exception->getMessage());
                $this->logger->error($exception->getTraceAsString());

                return $this->response
                    ->withHeader('Location', '/')
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
     * @throws HttpException
     */
    abstract protected function protectedAction(): Response;

    /**
     * Renders the given template with the given data
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
            ->render(0 === stripos($template, 'theme::') ? $template : "theme::$template", $data);
        $this->response->getBody()->write($renderResult);

        return $this->response
            ->withHeader('Content-Type', 'text/html')
            ->withStatus($statusCode);
    }

    /**
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     */
    protected function renderMenuItem(Database\MenuItem $menuItem): Response
    {
        if (null !== $menuItem->segmentPageId) {
            $segmentPage = $menuItem->getSegmentPage();

            return $this->render('theme::segment-page', ['page' => $segmentPage]);
        }

        if (null !== $menuItem->formId) {
            $form = $menuItem->getForm();

            return $this->render('theme::form', ['form' => $form]);
        }

        if (null !== $menuItem->artistId) {
            $artist = $menuItem->getArtist();

            return $this->render('theme::profile', ['artist' => $artist]);
        }

        if (null !== $menuItem->galleryId) {
            $gallery = $menuItem->getGallery();

            return $this->render('theme::gallery', ['gallery' => $gallery]);
        }

        if (null !== $menuItem->pageId) {
            $page = $menuItem->getPage();

            return $this->render('theme::simple-page', ['page' => $page]);
        }

        if (null !== $menuItem->categoryId) {
            $category = $menuItem->getBlogCategory();

            return $this->render('theme::blog-category', ['category' => $category, 'posts' => $category->getBlogPosts(true, true)]);
        }

        if ($menuItem->blogHomePage) {
            return $this->render('theme::blog-home-page', ['posts' => Database\BlogPost::findPublicPosts(), 'categories' => Database\BlogCategory::findAll()]);
        }

        if (Theming\Theme::ERROR_BEHAVIOR_HOMEPAGE === $this->activeTheme->getErrorBehavior()) {
            return $this->response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}
