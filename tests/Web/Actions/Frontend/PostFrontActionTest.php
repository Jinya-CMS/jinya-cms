<?php

namespace Jinya\Tests\Web\Actions\Frontend;

use App\Database\Form;
use App\Database\FormItem;
use App\Database\MenuItem;
use App\Tests\FrontTestCase;
use App\Web\Actions\Frontend\PostFrontAction;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class PostFrontActionTest extends FrontTestCase
{
    public function test__invoke(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $formItem = new FormItem();
        $formItem->position = 0;
        $formItem->formId = $form->getIdAsInt();
        $formItem->label = Uuid::uuid();
        $formItem->isFromAddress = true;
        $formItem->isSubject = true;
        $formItem->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->formId = $form->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([(string)$formItem->id => 'test@example.com']);
        $response = new Response();

        $action = new PostFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(302, $result->getStatusCode());
    }

    public function test__invokeMissingFields(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $formItem = new FormItem();
        $formItem->position = 0;
        $formItem->formId = $form->getIdAsInt();
        $formItem->label = Uuid::uuid();
        $formItem->isFromAddress = true;
        $formItem->isSubject = true;
        $formItem->isRequired = true;
        $formItem->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $this->menu->getIdAsInt();
        $menuItem->formId = $form->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = Uuid::uuid();
        $menuItem->position = 0;
        $menuItem->create();

        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([]);
        $response = new Response();

        $action = new PostFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(400, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([]);
        $response = new Response();

        $action = new PostFrontAction();
        $result = $action($request, $response, ['route' => 'test']);

        self::assertEquals(404, $result->getStatusCode());
    }
}
