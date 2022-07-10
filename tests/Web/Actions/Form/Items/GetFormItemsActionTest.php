<?php

namespace Jinya\Tests\Web\Actions\Form\Items;

use App\Database\Form;
use App\Database\FormItem;
use App\Web\Actions\Form\Items\GetFormItemsAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class GetFormItemsActionTest extends TestCase
{

    public function test__invoke(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $formItem = new FormItem();
        $formItem->label = Uuid::uuid();
        $formItem->formId = $form->getIdAsInt();
        $formItem->position = 0;
        $formItem->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetFormItemsAction();
        $result = $action($request, $response, ['id' => $form->getIdAsInt(), 'position' => 0]);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertCount(1, $body);
        self::assertEquals($formItem->format(), $body[0]);
    }

    public function test__invokeFormNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Form not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetFormItemsAction();
        $action($request, $response, ['id' => -1, 'position' => 0]);
    }
}
