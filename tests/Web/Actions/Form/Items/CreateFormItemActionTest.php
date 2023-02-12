<?php

namespace Jinya\Tests\Web\Actions\Form\Items;

use App\Database\Form;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Form\Items\CreateFormItemAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class CreateFormItemActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([
            'label' => Uuid::uuid(),
            'placeholder' => Uuid::uuid(),
            'position' => 0,
            'helpText' => Uuid::uuid(),
            'type' => Uuid::uuid(),
            'options' => [],
            'spamFilter' => [],
            'isFromAddress' => true,
            'isRequired' => true,
            'isSubject' => true,
        ]);
        $response = new Response();
        $action = new CreateFormItemAction();
        $result = $action($request, $response, ['id' => $form->getIdAsInt()]);

        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeFormNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Form not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new CreateFormItemAction();
        $action($request, $response, ['id' => -1, 'position' => 0]);
    }
}
