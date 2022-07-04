<?php

namespace Jinya\Tests\Routing;

use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\Utils\LoadableEntity;
use App\Routing\JinyaModelToRouteResolver;
use App\Web\Exceptions\MissingFieldsException;
use DateTime;
use Faker\Factory;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;

class JinyaModelToRouteResolverTest extends TestCase
{
    private ApiKey $apiKey;

    public function testResolveActionWithClassAndIdGetByIdNotEnoughPermissions(): void
    {
        $this->expectException(HttpForbiddenException::class);
        $artist = $this->createArtist(isAdmin: false, isWriter: false, isReader: false);
        $apiKey = new ApiKey();
        $apiKey->userId = $artist->getIdAsInt();
        $apiKey->setApiKey();
        $apiKey->remoteAddress = '127.0.0.1';
        $apiKey->validSince = new DateTime();
        $apiKey->userAgent = Factory::create()->userAgent();
        $apiKey->create();

        $request = new ServerRequest('GET', '', ['JinyaApiKey' => $apiKey->apiKey]);
        $response = new Response();
        JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist']);
    }

    public function testResolveActionWithClassAndIdEntityNotFound(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $request = new ServerRequest('GET', '', ['JinyaApiKey' => $this->apiKey->apiKey]);
        $response = new Response();
        JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'invalid']);
    }

    public function testResolveActionWithClassAndIdMethodNotAllowed(): void
    {
        $this->expectException(HttpMethodNotAllowedException::class);
        $request = new ServerRequest('PATCH', '', ['JinyaApiKey' => $this->apiKey->apiKey]);
        $response = new Response();
        JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist']);
    }

    public function testResolveActionWithClassAndIdDelete(): void
    {
        $artists = Artist::findAll();

        $request = new ServerRequest('DELETE', '', ['JinyaApiKey' => $this->apiKey->apiKey]);
        $response = new Response();
        $result = JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist', 'id' => $artists->current()->getIdAsInt()]);
        $result->getBody()->rewind();
        self::assertEquals(204, $result->getStatusCode());
        self::assertEmpty($result->getBody()->getContents());
        $artists->rewind();

        self::assertNotEquals(iterator_count($artists), iterator_count(Artist::findAll()));
    }

    public function testResolveActionWithClassAndIdDeleteNoId(): void
    {
        $this->expectException(HttpNotFoundException::class);

        $request = new ServerRequest('DELETE', '', ['JinyaApiKey' => $this->apiKey->apiKey]);
        $response = new Response();
        JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist']);
    }

    public function testResolveActionWithClassAndIdInvalidEntity(): void
    {
        $this->expectException(HttpNotFoundException::class);

        $request = new ServerRequest('GET', '', ['JinyaApiKey' => $this->apiKey->apiKey]);
        $response = new Response();
        JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'api-key', 'id' => CurrentUser::$currentUser->getIdAsInt()]);
    }

    public function testResolveActionWithClassAndIdGetById(): void
    {
        $request = new ServerRequest('GET', '', ['JinyaApiKey' => $this->apiKey->apiKey]);
        $response = new Response();
        $result = JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist', 'id' => CurrentUser::$currentUser->getIdAsInt()]);
        $result->getBody()->rewind();
        self::assertEquals(200, $result->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode(CurrentUser::$currentUser->format()), $result->getBody()->getContents());
    }

    public function testResolveActionWithClassAndIdGetListAll(): void
    {
        $artists = Artist::findAll();

        $request = new ServerRequest('GET', '', ['JinyaApiKey' => $this->apiKey->apiKey]);
        $response = new Response();
        $result = JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist']);
        $result->getBody()->rewind();
        self::assertEquals(200, $result->getStatusCode());

        $data = iterator_to_array($artists);
        $count = count($data);
        self::assertJsonStringEqualsJsonString(json_encode([
            'offset' => 0,
            'itemsCount' => $count,
            'totalCount' => $count,
            'items' => array_map(static fn(Artist $item) => $item->format(), $data),
        ], JSON_THROW_ON_ERROR), $result->getBody()->getContents());
    }

    public function testResolveActionWithClassAndIdGetListKeyword(): void
    {
        $allArtists = Artist::findAll();
        $artists = Artist::findByKeyword($allArtists->current()->artistName);

        $request = new ServerRequest('GET', '', ['JinyaApiKey' => $this->apiKey->apiKey]);
        $response = new Response();
        $result = JinyaModelToRouteResolver::resolveActionWithClassAndId($request->withQueryParams(['keyword' => $allArtists->current()->artistName]), $response, ['entity' => 'artist']);
        $result->getBody()->rewind();
        self::assertEquals(200, $result->getStatusCode());

        $data = iterator_to_array($artists);
        $count = count($data);
        self::assertJsonStringEqualsJsonString(json_encode([
            'offset' => 0,
            'itemsCount' => $count,
            'totalCount' => $count,
            'items' => array_map(static fn(Artist $item) => $item->format(), $data),
        ], JSON_THROW_ON_ERROR), $result->getBody()->getContents());
    }

    public function testResolveActionWithClassAndIdPut(): void
    {
        $firstArtist = Artist::findAll()->current()->getIdAsInt();
        $newArtistName = Factory::create()->name();
        $body = (new Psr17Factory())->createStream(json_encode([
            'artistName' => $newArtistName,
        ], JSON_THROW_ON_ERROR));
        $body->rewind();
        $request = new ServerRequest('PUT', '', ['JinyaApiKey' => $this->apiKey->apiKey], $body);
        $response = new Response();
        $result = JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist', 'id' => $firstArtist]);
        $result->getBody()->rewind();
        self::assertEquals(204, $result->getStatusCode());
        self::assertEmpty($result->getBody()->getContents());

        $artist = Artist::findById($firstArtist);
        self::assertEquals($newArtistName, $artist->artistName);
    }

    public function testResolveActionWithClassAndIdPutNoId(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $newArtistName = Factory::create()->name();
        $body = (new Psr17Factory())->createStream(json_encode([
            'artistName' => $newArtistName,
        ], JSON_THROW_ON_ERROR));
        $body->rewind();
        $request = new ServerRequest('PUT', '', ['JinyaApiKey' => $this->apiKey->apiKey], $body);
        $response = new Response();
        JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist']);
    }

    public function testResolveActionWithClassAndIdPost(): void
    {
        $faker = Factory::create();
        $data = [
            'artistName' => $faker->name(),
            'email' => $faker->email(),
            'aboutMe' => $faker->uuid(),
            'profilePicture' => $faker->filePath(),
            'enabled' => $faker->boolean(),
            'roles' => ['ROLE_READER'],
        ];
        $body = (new Psr17Factory())->createStream(json_encode($data, JSON_THROW_ON_ERROR));
        $body->rewind();
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->apiKey->apiKey], $body);
        $response = new Response();
        $result = JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist']);
        $result->getBody()->rewind();
        $resultBody = $result->getBody()->getContents();
        self::assertEquals(201, $result->getStatusCode());
        $artist = Artist::findByEmail($data['email']);
        self::assertJsonStringEqualsJsonString($resultBody, json_encode($artist->format()));
    }

    public function testResolveActionWithClassAndIdPostMissingField(): void
    {
        $this->expectException(MissingFieldsException::class);
        $faker = Factory::create();
        $data = [
            'artistName' => $faker->name(),
            'aboutMe' => $faker->uuid(),
            'profilePicture' => $faker->filePath(),
            'enabled' => $faker->boolean(),
            'roles' => ['ROLE_READER'],
        ];
        $body = (new Psr17Factory())->createStream(json_encode($data, JSON_THROW_ON_ERROR));
        $body->rewind();
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->apiKey->apiKey], $body);
        $response = new Response();
        JinyaModelToRouteResolver::resolveActionWithClassAndId($request, $response, ['entity' => 'artist']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        LoadableEntity::executeSqlString('DELETE FROM users');
        $artist = new Artist();
        $artist->email = 'firstuser@example.com';
        $artist->aboutMe = 'About me';
        $artist->profilePicture = 'profilepicture';
        $artist->artistName = 'First user';
        $artist->enabled = true;
        $artist->roles = [];
        $artist->setPassword('start1234');
        $artist->roles[] = 'ROLE_READER';
        $artist->roles[] = 'ROLE_WRITER';
        $artist->roles[] = 'ROLE_ADMIN';

        $artist->create();
        CurrentUser::$currentUser = $artist;

        for ($i = 0; $i < 7; ++$i) {
            $this->createArtist();
        }
        $this->apiKey = new ApiKey();
        $this->apiKey->userId = CurrentUser::$currentUser->getIdAsInt();
        $this->apiKey->setApiKey();
        $this->apiKey->remoteAddress = '127.0.0.1';
        $this->apiKey->validSince = new DateTime();
        $this->apiKey->userAgent = Factory::create()->userAgent();
        $this->apiKey->create();
    }

    private function createArtist(bool $isAdmin = true, bool $isWriter = true, bool $isReader = true, bool $enabled = true, string $password = 'test1234'): Artist
    {
        $artist = new Artist();
        $artist->email = Uuid::uuid() . Factory::create()->safeEmail();
        $artist->aboutMe = 'About me';
        $artist->profilePicture = 'profilepicture';
        $artist->artistName = Uuid::uuid();
        $artist->enabled = $enabled;
        $artist->roles = [];
        $artist->setPassword($password);
        if ($isAdmin) {
            $artist->roles[] = 'ROLE_ADMIN';
        }
        if ($isReader) {
            $artist->roles[] = 'ROLE_READER';
        }
        if ($isWriter) {
            $artist->roles[] = 'ROLE_WRITER';
        }
        $artist->create();

        return $artist;
    }
}
