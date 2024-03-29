<?php

namespace Jinya\Tests\Web\Controllers;

use App\Database\Form;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\FormController;
use Faker\Provider\Uuid;
use Nyholm\Psr7\ServerRequest;

class FormControllerTest extends DatabaseAwareTestCase
{
    private function getController(array $body): FormController
    {
        $controller = new FormController();
        $controller->request = (new ServerRequest('', ''))->withParsedBody($body);
        $controller->body = $body;

        return $controller;
    }

    public function testGetItems(): void
    {
        $form = new Form();
        $form->title = 'Title';
        $form->toAddress = 'test@example.com';
        $form->create();

        $form->replaceItems([
            [
                'formId' => $form->id,
                'label' => Uuid::uuid(),
                'isFromAddress' => true,
                'isSubject' => true,
                'isRequired' => true,
                'type' => 'text',
            ]
        ]);

        $controller = $this->getController([]);
        $result = $controller->getItems($form->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertCount(1, $body);
    }

    public function testGetItemsPostNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getItems(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Form not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testReplaceSegments(): void
    {
        $form = new Form();
        $form->title = 'Title';
        $form->toAddress = 'test@example.com';
        $form->create();

        $controller = $this->getController([
            [
                'formId' => $form->id,
                'label' => Uuid::uuid(),
                'isFromAddress' => true,
                'isSubject' => true,
                'isRequired' => true,
                'type' => 'text',
            ]
        ]);
        $result = $controller->replaceItems($form->id);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testReplaceSegmentsPostNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->replaceItems(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Form not found',
                'type' => 'not-found',
            ],
        ], $body);
    }
}
