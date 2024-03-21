<?php

namespace Jinya\Tests\Web\Actions\SegmentPage\Segments;

use App\Database\Form;
use App\Database\SegmentPage;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\SegmentPage\Segments\CreateFormSegmentAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class CreateFormSegmentActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'noreply@example.com';
        $form->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([
            'form' => $form->getIdAsInt(),
            'position' => 0,
        ]);
        $response = new Response();
        $action = new CreateFormSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt()]);

        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeSegmentPageNotFound(): void
    {
        $this->expectExceptionMessage('Segment page not found');
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new CreateFormSegmentAction();
        $action($request, $response, ['id' => -1]);
    }

    public function test__invokeFormNotFound(): void
    {
        $this->expectExceptionMessage('Form not found');
        $this->expectException(NoResultException::class);
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([
            'form' => -1,
            'position' => 0,
        ]);
        $response = new Response();
        $action = new CreateFormSegmentAction();
        $action($request, $response, ['id' => $segmentPage->getIdAsInt()]);
    }
}
