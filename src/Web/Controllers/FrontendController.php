<?php

namespace App\Web\Controllers;

use App\Database;
use App\Database\BlogPost;
use App\Database\MenuItem;
use App\Database\Theme;
use App\Logging\Logger;
use App\Messaging\FormMessageHandler;
use App\Theming;
use Jinya\Plates\Engine;
use Jinya\Plates\Extension\URI;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Route;
use Jinya\Router\Extensions\Database\Exceptions\MissingFieldsException;
use JsonException;
use Nyholm\Psr7\Response;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

#[Controller]
class FrontendController extends BaseController
{
    private readonly Theming\Theme $activeTheme;
    private readonly LoggerInterface $logger;
    private Engine $engine;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->activeTheme = new Theming\Theme(Database\Theme::getActiveTheme());
        $this->logger = Logger::getLogger();

        $this->engine = Theming\Engine::getPlatesEngine();
        $currentDbTheme = Database\Theme::getActiveTheme();
        $this->engine->loadExtension($this->activeTheme);
        $this->engine->loadExtension(new Theming\Extensions\MenuExtension());
        $this->engine->loadExtension(new Theming\Extensions\FileExtension());
        $this->engine->loadExtension(new Theming\Extensions\LinksExtension($currentDbTheme));
        $this->engine->loadExtension(new Theming\Extensions\ThemeExtension($this->activeTheme, $currentDbTheme));
    }

    /**
     * Executes the front action. If an error occurs, either the homepage or a page with the error code will be returned. If all error handling fails, the emergency website will be shown
     *
     * @throws Throwable
     */
    private function executeErrorHandled(callable $handler): ResponseInterface
    {
        try {
            return $handler();
        } catch (Throwable $exception) {
            if (Theming\Theme::ERROR_BEHAVIOR_HOMEPAGE === $this->activeTheme->getErrorBehavior()) {
                $this->logger->error($exception->getMessage());
                $this->logger->error($exception->getTraceAsString());

                return new Response(self::HTTP_FOUND, ['Location' => '/']);
            }

            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
            $statusCode = self::HTTP_INTERNAL_SERVER_ERROR;

            try {
                $data = ['exception' => $exception];
                if ($this->checkForApiRequest()) {
                    return $this->sendApiJson((string)$statusCode, $data, $statusCode);
                }

                return $this->renderThemed((string)$statusCode, $data, $statusCode);
            } catch (Throwable $exception) {
                $this->logger->error($exception->getMessage());
                $this->logger->error($exception->getTraceAsString());

                $engine = Theming\Engine::getPlatesEngine();
                $renderResult = $engine->render('emergency::500', ['exception' => $exception]);

                return new Response(self::HTTP_INTERNAL_SERVER_ERROR, ['Content-Type' => 'text/html'], $renderResult);
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function checkForApiRequest(): bool
    {
        $acceptHeader = strtolower($this->getHeader('Accept'));

        return Theme::getActiveTheme()->hasApiTheme && str_starts_with($acceptHeader, 'application/json');
    }

    /**
     * Sends the given data as json response
     * @param string $template
     * @param array<mixed> $data
     * @param int $statusCode
     * @return ResponseInterface
     * @throws Throwable
     */
    private function sendApiJson(string $template, array $data, int $statusCode = self::HTTP_OK): ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();
        $queryParams = $this->request->getQueryParams();
        if (!$this->engine->functions->exists('uri')) {
            $this->engine->loadExtension(new URI($this->request->getUri()->getPath()));
        }
        $renderResult = $this->engine->render("api::$template", [
            ...$data,
            'body' => $parsedBody,
            'queryParams' => $queryParams,
        ]);

        return new Response($statusCode, ['Content-Type' => 'application/json'], $renderResult);
    }

    /**
     * Renders the given template with the given data
     * @param string $template
     * @param array<mixed> $data
     * @param int $statusCode
     * @return ResponseInterface
     * @throws Throwable
     */
    private function renderThemed(string $template, array $data, int $statusCode = self::HTTP_OK): ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();
        $queryParams = $this->request->getQueryParams();
        if (!$this->engine->functions->exists('uri')) {
            $this->engine->loadExtension(new URI($this->request->getUri()->getPath()));
        }
        $renderResult = $this->engine->render("theme::$template", [
            ...$data,
            'body' => $parsedBody,
            'queryParams' => $queryParams,
        ]);

        return new Response($statusCode, ['Content-Type' => 'text/html'], $renderResult);
    }

    /**
     * @param MenuItem $menuItem
     * @return ResponseInterface
     * @throws Throwable
     */
    private function renderMenuItem(Database\MenuItem $menuItem): ResponseInterface
    {
        $data = [];
        $status = self::HTTP_OK;

        if ($menuItem->modernPageId !== null) {
            $segmentPage = $menuItem->getModernPage();
            $template = 'segment-page';
            $data = ['page' => $segmentPage];
        } elseif ($menuItem->formId !== null) {
            $form = $menuItem->getForm();
            $template = 'form';
            $data = [
                'form' => $form,
                'success' => false,
                'missingFields' => [],
            ];
        } elseif ($menuItem->artistId !== null) {
            $artist = $menuItem->getArtist();
            $template = 'profile';
            $data = ['artist' => $artist];
        } elseif ($menuItem->galleryId !== null) {
            $gallery = $menuItem->getGallery();
            $template = 'gallery';
            $data = ['gallery' => $gallery];
        } elseif ($menuItem->classicPageId !== null) {
            $page = $menuItem->getClassicPage();
            $template = 'simple-page';
            $data = ['page' => $page];
        } elseif ($menuItem->categoryId !== null) {
            $category = $menuItem->getBlogCategory();
            $template = 'blog-category';
            $data = ['category' => $category, 'posts' => $category?->getBlogPosts(true, true)];
        } elseif ($menuItem->blogHomePage) {
            $template = 'blog-home-page';
            $data = ['posts' => Database\BlogPost::findPublicPosts()];
        } else {
            if (Theming\Theme::ERROR_BEHAVIOR_HOMEPAGE === $this->activeTheme->getErrorBehavior()) {
                return new Response(self::HTTP_FOUND, ['Location' => '/']);
            }

            $template = '404';
            $status = self::HTTP_NOT_FOUND;
        }

        if (Theming\Theme::ERROR_BEHAVIOR_HOMEPAGE === $this->activeTheme->getErrorBehavior()) {
            return new Response(self::HTTP_FOUND, ['Location' => '/']);
        }

        if ($this->checkForApiRequest()) {
            return $this->sendApiJson($template, $data, $status);
        }

        return $this->renderThemed($template, $data, $status);
    }

    /**
     * Renders the given frontend route or a matching blog post if there is no menu item with the given route
     *
     * @param string $route
     * @return ResponseInterface
     * @throws Throwable
     */
    #[Route(HttpMethod::GET, '/[{route:.*}]')]
    public function frontend(string $route = ''): ResponseInterface
    {
        return $this->executeErrorHandled(function () use ($route) {
            if ($route === '' || $route === '/') {
                if ($this->checkForApiRequest()) {
                    return $this->sendApiJson('home', []);
                }

                return $this->renderThemed('home', []);
            }

            $menuItem = MenuItem::findByRoute($route);
            if ($menuItem !== null) {
                return $this->renderMenuItem($menuItem);
            }

            return $this->blogFrontend($route);
        });
    }

    /**
     * Executes the form post. If the form is not found the 404 page will be returned
     *
     * @param string $route
     * @return ResponseInterface
     * @throws Throwable
     * @throws Exception
     */
    #[Route(HttpMethod::POST, '/[{route:.*}]')]
    public function postForm(string $route): ResponseInterface
    {
        $menuItem = MenuItem::findByRoute($route);
        if ($menuItem !== null) {
            if ($menuItem->formId !== null) {
                $form = $menuItem->getForm();
                $formHandler = new FormMessageHandler();
                try {
                    if ($form !== null) {
                        $data = $_POST;
                        if ($this->getHeader('Content-Type') === 'application/json') {
                            $data = $this->body;
                        }

                        $formHandler->handleFormPost($form, $data, $this->request);
                    }

                    $responseData = ['form' => $form, 'success' => true];
                    if ($this->checkForApiRequest()) {
                        return $this->sendApiJson('form', $responseData, self::HTTP_FOUND);
                    }

                    return $this->renderThemed('form', $responseData, self::HTTP_FOUND);
                } catch (MissingFieldsException $exception) {
                    $responseData = [
                        'form' => $form,
                        'success' => false,
                        'missingFields' => $exception->missingFields,
                    ];

                    if ($this->checkForApiRequest()) {
                        return $this->sendApiJson('form', $responseData, self::HTTP_BAD_REQUEST);
                    }

                    return $this->renderThemed('form', $responseData, self::HTTP_BAD_REQUEST);
                }
            }

            return $this->renderMenuItem($menuItem);
        }

        if ($this->checkForApiRequest()) {
            return $this->sendApiJson('404', [], self::HTTP_NOT_FOUND);
        }

        return $this->renderThemed('404', [], self::HTTP_NOT_FOUND);
    }

    /**
     * Displays the given blog post or returns a 404 page
     * @throws Throwable
     */
    #[Route(HttpMethod::GET, '/{year:\d\d\d\d}/{month:\d\d}/{day:\d\d}/{slug}')]
    public function blogFrontend(string $slug): ResponseInterface
    {
        return $this->executeErrorHandled(function () use ($slug) {
            $blogPost = BlogPost::findBySlug($slug);
            if ($blogPost !== null && $blogPost->public) {
                $data = ['post' => $blogPost];

                if ($this->checkForApiRequest()) {
                    return $this->sendApiJson('blog-post', $data);
                }

                return $this->renderThemed('blog-post', $data);
            }

            if ($this->checkForApiRequest()) {
                return $this->sendApiJson('404', [], self::HTTP_NOT_FOUND);
            }

            return $this->renderThemed('404', [], self::HTTP_NOT_FOUND);
        });
    }

    /**
     * @throws Throwable
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/frontend/links')]
    public function getLinks(): ResponseInterface
    {
        if (!Database\Theme::getActiveTheme()->hasApiTheme) {
            return $this->notFound();
        }

        return $this->executeErrorHandled(fn () => $this->sendApiJson('api::links', []));
    }
}
