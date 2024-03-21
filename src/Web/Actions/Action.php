<?php

namespace App\Web\Actions;

use App\Database\Utils\FormattableEntityInterface;
use App\Logging\Logger;
use App\Storage\StorageBaseService;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

/**
 * Base class for all http actions that can't be generated with reflection
 */
abstract class Action
{
    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;
    public const HTTP_EARLY_HINTS = 103;
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207;
    public const HTTP_ALREADY_REPORTED = 208;
    public const HTTP_IM_USED = 226;
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENTLY_REDIRECT = 308;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_I_AM_A_TEAPOT = 418;
    public const HTTP_MISDIRECTED_REQUEST = 421;
    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    public const HTTP_LOCKED = 423;
    public const HTTP_FAILED_DEPENDENCY = 424;
    public const HTTP_TOO_EARLY = 425;
    public const HTTP_UPGRADE_REQUIRED = 426;
    public const HTTP_PRECONDITION_REQUIRED = 428;
    public const HTTP_TOO_MANY_REQUESTS = 429;
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;
    public const HTTP_INSUFFICIENT_STORAGE = 507;
    public const HTTP_LOOP_DETECTED = 508;
    public const HTTP_NOT_EXTENDED = 510;
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /** @var array<string, mixed> The query parameters */
    public array $queryParams;

    /** @var LoggerInterface The logger for the action */
    protected LoggerInterface $logger;

    /** @var Request The parsed http request */
    protected Request $request;

    /** @var Response The http response for this action */
    protected Response $response;

    /** @var array<string, int|string|float|mixed> The arguments parsed by slim from fast route */
    protected array $args;

    /** @var array<string, mixed> The parsed body of the action */
    protected array $body;

    /**
     * Creates a new Action
     */
    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

    /**
     * Invokes the class like a method
     *
     * @param Request $request
     * @param Response $response
     * @param array<string, mixed> $args
     * @return Response
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $this->body = is_array($request->getParsedBody()) ? $request->getParsedBody() : [];
        $this->queryParams = $request->getQueryParams();

        return $this->action();
    }

    /**
     * The action to run when the request gets executed
     *
     * @return Response
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    /**
     * Responds the given data formatted as list
     *
     * @param array<mixed> $data
     * @param int $offset
     * @param int $count
     * @return Response
     */
    protected function respondList(array $data, int $offset = 0, int $count = -1): Response
    {
        return $this->respond($this->formatList($data, $offset, $count));
    }

    /**
     * Responds the given payload with the given http status code as json
     *
     * @param mixed $payload
     * @param int $statusCode
     * @param int $jsonFlags
     * @return Response
     */
    protected function respond(mixed $payload = null, int $statusCode = Action::HTTP_OK, int $jsonFlags = JSON_THROW_ON_ERROR): Response
    {
        $json = json_encode($payload, $jsonFlags) ?: '';
        $this->response->getBody()->write($json);

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    /**
     * Formats a list to the common list response format
     *
     * @param array<mixed> $data
     * @param int $offset
     * @param int $count
     * @return array<string, mixed|int|void>
     */
    #[ArrayShape([
        'offset' => 'int',
        'itemsCount' => 'int|void',
        'totalCount' => 'int|void',
        'items' => 'array'
    ])] protected function formatList(array $data, int $offset = 0, int $count = -1): array
    {
        return [
            'offset' => $offset,
            'itemsCount' => count($data),
            'totalCount' => $count === -1 ? count($data) : $count,
            'items' => $data,
        ];
    }

    /**
     * Generates a file response
     *
     * @param string $path
     * @param string $contentType
     * @param string $basePath
     * @return Response
     */
    protected function respondFile(
        string $path,
        string $contentType = 'application/octet-stream',
        string $basePath = StorageBaseService::BASE_PATH . '/public/'
    ): Response {
        return $this->response
            ->withBody(Stream::create(fopen($basePath . $path, 'rb') ?: ''))
            ->withHeader('Content-Type', $contentType)
            ->withStatus(self::HTTP_OK);
    }

    /**
     * Generates an HTTP 204 No Content response
     *
     * @return Response
     */
    protected function noContent(): Response
    {
        return $this->respond([], self::HTTP_NO_CONTENT);
    }

    /**
     * Runs the format method on every item in the given iterator
     *
     * @param Iterator $iterator
     * @return array<mixed>
     */
    protected function formatIterator(Iterator $iterator): array
    {
        $data = [];
        foreach ($iterator as $item) {
            /** @var FormattableEntityInterface $item */
            $data[] = $item->format();
        }

        return $data;
    }
}
