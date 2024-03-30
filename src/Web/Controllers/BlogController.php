<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\BlogCategory;
use Jinya\Cms\Database\BlogPost;
use Jinya\Cms\Database\Exceptions\TransactionFailedException;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class BlogController extends BaseController
{
    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/blog-category/{id}/post')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getPostsByCategory(int $id): ResponseInterface
    {
        $category = BlogCategory::findById($id);
        if ($category === null) {
            return $this->entityNotFound('Blog category not found');
        }

        $posts = $category->getBlogPosts(true);

        return $this->jsonIterator($posts);
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/blog-post/{id}/section')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getSections(int $id): ResponseInterface
    {
        $post = BlogPost::findById($id);
        if ($post === null) {
            return $this->entityNotFound('Blog post not found');
        }

        $sections = $post->getSections();

        return $this->jsonIteratorPlain($sections);
    }

    /**
     * @throws JsonException
     * @throws TransactionFailedException
     */
    #[Route(HttpMethod::PUT, '/api/blog-post/{id}/section')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function replaceSections(int $id): ResponseInterface
    {
        $post = BlogPost::findById($id);
        if ($post === null) {
            return $this->entityNotFound('Blog post not found');
        }

        /** @var array<int, mixed> $body */
        $body = $this->body;
        $post->replaceSections($body);

        return $this->noContent();
    }
}
