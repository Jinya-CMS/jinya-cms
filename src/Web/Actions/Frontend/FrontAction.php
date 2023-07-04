<?php

namespace App\Web\Actions\Frontend;

use App\Database;
use App\Database\Theme;
use App\Theming;
use App\Web\Actions\Action;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use League\Plates\Engine;
use League\Plates\Extension\URI;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpException;
use Throwable;

/**
 * The base action for all front actions
 */
abstract class FrontAction extends Action
{
    /** @var Engine The Plates engine */
    protected Engine $engine;

    /** @var Theming\Theme The active theming theme */
    protected Theming\Theme $activeTheme;

    /**
     * FrontAction constructor.
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws NoResultException
     */
    public function __construct()
    {
        parent::__construct();
        $this->engine = Theming\Engine::getPlatesEngine();
        $this->activeTheme = new Theming\Theme(Database\Theme::getActiveTheme());
    }

    /**
     * Executes the front action. If an error occurs either the homepage or a page with the error code will be returned. If all error handling fails, the emergency website will be shown
     *
     * @throws Throwable
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
                if ($this->checkForApiRequest()) {
                    return $this->sendApiJson("api::$statusCode", ['exception' => $exception], $statusCode);
                }

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

    protected function checkForApiRequest(): bool
    {
        $currentThemeHasApi = Theme::getActiveTheme()->hasApiTheme;
        if ($currentThemeHasApi) {
            $acceptHeader = strtolower($this->request->getHeaderLine('Accept'));
            $acceptMimeType = [];
            preg_match_all('/(?!\s)(?:"(?:[^"\\\\]|\\\\.)*(?:"|\\\\|$)|[^", ]+)+(?<!\s)|\s*(?<separator>[, ])\s*/x', $acceptHeader, $acceptMimeType, PREG_SET_ORDER);
            if ($acceptMimeType && $acceptMimeType[0][0] === 'application/json') {
                return true;
            }
        }

        return false;
    }

    /**
     * Renders the given template with the given data
     * @param string $template
     * @param array<mixed> $data
     * @param int $statusCode
     * @return Response
     * @throws Throwable
     */
    protected function render(string $template, array $data, int $statusCode = self::HTTP_OK): Response
    {
        $parsedBody = $this->request->getParsedBody();
        $queryParams = $this->request->getQueryParams();
        $this->engine->addData(['body' => $parsedBody, 'queryParams' => $queryParams]);
        $this->engine->loadExtension($this->activeTheme);
        $currentDbTheme = Database\Theme::getActiveTheme();
        $this->engine->loadExtension(new Theming\Extensions\MenuExtension());
        $this->engine->loadExtension(new Theming\Extensions\FileExtension());
        $this->engine->loadExtension(new Theming\Extensions\LinksExtension($currentDbTheme));
        $this->engine->loadExtension(new Theming\Extensions\ThemeExtension($this->activeTheme, $currentDbTheme));
        $this->engine->loadExtension(new URI($this->request->getUri()->getPath()));

        $renderResult = $this->engine
            ->render(strncasecmp($template, 'theme::', 7) === 0 ? $template : "theme::$template", $data);
        $this->response->getBody()->write($renderResult);

        return $this->response
            ->withHeader('Content-Type', 'text/html')
            ->withStatus($statusCode);
    }

    /**
     * Sends the given data as json response
     * @param string $template
     * @param array<mixed> $data
     * @param int $statusCode
     * @return Response
     * @throws Throwable
     */
    protected function sendApiJson(string $template, array $data, int $statusCode = self::HTTP_OK): Response
    {
        $parsedBody = $this->request->getParsedBody();
        $queryParams = $this->request->getQueryParams();
        $this->engine->addData(['body' => $parsedBody, 'queryParams' => $queryParams]);
        $this->engine->loadExtension($this->activeTheme);
        $currentDbTheme = Database\Theme::getActiveTheme();
        $this->engine->loadExtension(new Theming\Extensions\MenuExtension());
        $this->engine->loadExtension(new Theming\Extensions\LinksExtension($currentDbTheme));
        $this->engine->loadExtension(new Theming\Extensions\ThemeExtension($this->activeTheme, $currentDbTheme));
        $this->engine->loadExtension(new URI($this->request->getUri()->getPath()));

        $renderResult = $this->engine
            ->render(strncasecmp($template, 'api::', 5) === 0 ? $template : "api::$template", $data);
        $this->response->getBody()->write($renderResult);

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    /**
     * @param Database\MenuItem $menuItem
     * @return Response
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws Throwable
     */
    protected function renderMenuItem(Database\MenuItem $menuItem): Response
    {
        if ($menuItem->segmentPageId !== null) {
            $segmentPage = $menuItem->getSegmentPage();
            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('api::segment-page', ['page' => $segmentPage]);
            }

            return $this->render('theme::segment-page', ['page' => $segmentPage]);
        }

        if ($menuItem->formId !== null) {
            $form = $menuItem->getForm();
            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('api::form', [
                    'form' => $form,
                    'success' => false,
                    'missingFields' => [],
                ]);
            }

            return $this->render('theme::form', [
                'form' => $form,
                'success' => false,
                'missingFields' => [],
            ]);
        }

        if ($menuItem->artistId !== null) {
            $artist = $menuItem->getArtist();
            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('api::profile', ['artist' => $artist]);
            }

            return $this->render('theme::profile', ['artist' => $artist]);
        }

        if ($menuItem->galleryId !== null) {
            $gallery = $menuItem->getGallery();
            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('api::gallery', ['gallery' => $gallery]);
            }

            return $this->render('theme::gallery', ['gallery' => $gallery]);
        }

        if ($menuItem->pageId !== null) {
            $page = $menuItem->getPage();
            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('api::simple-page', ['page' => $page]);
            }

            return $this->render('theme::simple-page', ['page' => $page]);
        }

        if ($menuItem->categoryId !== null) {
            $category = $menuItem->getBlogCategory();
            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('api::blog-category', ['category' => $category, 'posts' => $category?->getBlogPosts(true, true)]);
            }

            return $this->render('theme::blog-category', ['category' => $category, 'posts' => $category?->getBlogPosts(true, true)]);
        }

        if ($menuItem->blogHomePage) {
            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('api::blog-home-page', ['posts' => Database\BlogPost::findPublicPosts()]);
            }

            return $this->render('theme::blog-home-page', ['posts' => Database\BlogPost::findPublicPosts()]);
        }

        if (Theming\Theme::ERROR_BEHAVIOR_HOMEPAGE === $this->activeTheme->getErrorBehavior()) {
            return $this->response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }
        if ($this->checkForApiRequest()) {
            return $this->sendApiJson('api::404', [], self::HTTP_NOT_FOUND);
        }

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}
