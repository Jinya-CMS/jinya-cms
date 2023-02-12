<?php

namespace Jinya\Tests\Web\Actions\SegmentPage\Segments;

use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Segment;
use App\Database\SegmentPage;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\SegmentPage\Segments\UpdateSegmentAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Slim\Exception\HttpNotFoundException;

class UpdateSegmentActionTest extends DatabaseAwareTestCase
{

    public function test__invokeSegmentNotFound(): void
    {
        $this->expectExceptionMessage('Segment not found');
        $this->expectException(HttpNotFoundException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UpdateSegmentAction();
        $action($request, $response, ['id' => -1, 'position' => -1]);
    }

    public function test__invokeFile(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        $segment = new Segment();
        $segment->fileId = $file->getIdAsInt();
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['file' => $file->getIdAsInt(), 'action' => 'none', 'newPosition' => 1]);
        $response = new Response();
        $action = new UpdateSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeFileLink(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        $segment = new Segment();
        $segment->fileId = $file->getIdAsInt();
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['file' => $file->getIdAsInt(), 'action' => 'link', 'link' => 'example.com']);
        $response = new Response();
        $action = new UpdateSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeFileScript(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        $segment = new Segment();
        $segment->fileId = $file->getIdAsInt();
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['file' => $file->getIdAsInt(), 'action' => 'script', 'script' => 'example.com']);
        $response = new Response();
        $action = new UpdateSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeFileNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('File not found');
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        $segment = new Segment();
        $segment->fileId = $file->getIdAsInt();
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['file' => -1]);
        $response = new Response();
        $action = new UpdateSegmentAction();
        $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);
    }

    public function test__invokeGallery(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->create();

        $segment = new Segment();
        $segment->galleryId = $gallery->getIdAsInt();
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['gallery' => $gallery->getIdAsInt()]);
        $response = new Response();
        $action = new UpdateSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeGalleryNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Gallery not found');
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->create();

        $segment = new Segment();
        $segment->galleryId = $gallery->getIdAsInt();
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['gallery' => -1]);
        $response = new Response();
        $action = new UpdateSegmentAction();
        $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);
    }

    public function test__invokeHtml(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $segment = new Segment();
        $segment->html = Uuid::uuid();
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['html' => Uuid::uuid()]);
        $response = new Response();
        $action = new UpdateSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeForm(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'noreply@example.com';
        $form->create();

        $segment = new Segment();
        $segment->formId = $form->getIdAsInt();
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['form' => $form->getIdAsInt()]);
        $response = new Response();
        $action = new UpdateSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeFormNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Form not found');
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'noreply@example.com';
        $form->create();

        $segment = new Segment();
        $segment->formId = $form->getIdAsInt();
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody(['form' => -1]);
        $response = new Response();
        $action = new UpdateSegmentAction();
        $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);
    }
}
