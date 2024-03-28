<?php

namespace Jinya\Tests\Web\Controllers\Form\Items;

use App\Database\Form;
use App\Database\FormItem;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\Form\Items\DeleteFormItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class DeleteFormItemActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $formItem = new FormItem();
        $formItem->label = Uuid::uuid();
        $formItem->formId = $form->id;
        $formItem->position = 0;
        $formItem->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeleteFormItemAction();
        $result = $action($request, $response, ['id' => $form->id, 'position' => 0]);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeItemNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Form item not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeleteFormItemAction();
        $action($request, $response, ['id' => -1, 'position' => 0]);
    }
}
