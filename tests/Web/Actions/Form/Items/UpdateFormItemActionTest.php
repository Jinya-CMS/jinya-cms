<?php

namespace Jinya\Tests\Web\Actions\Form\Items;

use App\Database\Form;
use App\Database\FormItem;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Form\Items\UpdateFormItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class UpdateFormItemActionTest extends DatabaseAwareTestCase
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
        $request = $request->withParsedBody([
            'label' => Uuid::uuid(),
            'placeholder' => Uuid::uuid(),
            'newPosition' => 0,
            'helpText' => Uuid::uuid(),
            'type' => Uuid::uuid(),
            'options' => [],
            'spamFilter' => [],
            'isFromAddress' => true,
            'isRequired' => true,
            'isSubject' => true,
        ]);
        $response = new Response();
        $action = new UpdateFormItemAction();
        $result = $action($request, $response, ['id' => $form->getIdAsInt(), 'position' => 0]);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeFormNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Form item not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UpdateFormItemAction();
        $action($request, $response, ['id' => -1, 'position' => 0]);
    }
}
