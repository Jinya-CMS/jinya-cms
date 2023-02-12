<?php

namespace Jinya\Tests\Messaging;

use App\Database\Form;
use App\Database\FormItem;
use App\Messaging\FormMessageHandler;
use App\Tests\DatabaseAwareTestCase;
use App\Theming\Engine;
use App\Web\Exceptions\MissingFieldsException;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class FormMessageHandlerTest extends DatabaseAwareTestCase
{
    private FormMessageHandler $handler;
    private Form $form;
    private ServerRequestInterface $request;

    public function testRenderTemplate(): void
    {
        $engine = Engine::getPlatesEngine();
        $engine->addFolder('messaging', __ROOT__ . '/src/Messaging/Templates');
        $expected = $engine->render('messaging::new', ['data' => ['key' => 'value'], 'title' => 'Start']);

        $actual = $this->handler->renderTemplate(['key' => 'value'], 'Start');

        self::assertEquals($expected, $actual);
    }

    public function testRenderTemplateEmptyData(): void
    {
        $engine = Engine::getPlatesEngine();
        $engine->addFolder('messaging', __ROOT__ . '/src/Messaging/Templates');
        $expected = $engine->render('messaging::new', ['data' => [], 'title' => 'Start']);

        $actual = $this->handler->renderTemplate([], 'Start');

        self::assertEquals($expected, $actual);
    }

    public function testHandleFormPost(): void
    {
        $items = $this->form->getItems();
        $data = [];
        foreach ($items as $item) {
            /** @var FormItem $item */
            switch ($item->type) {
                case 'email':
                    $data[$item->getIdAsInt()] = 'test@example.com';
                    break;
                case 'checkbox':
                    $data[$item->getIdAsInt()] = true;
                    break;
                case 'text':
                case 'select':
                    $data[$item->getIdAsInt()] = 'Test';
                    break;
            }
        }
        $this->handler->handleFormPost($this->form, $data, $this->request);
        self::assertTrue(true);
    }

    public function testHandleFormPostMissingFields(): void
    {
        $this->expectException(MissingFieldsException::class);
        $items = $this->form->getItems();
        $data = [];
        foreach ($items as $item) {
            /** @var FormItem $item */
            switch ($item->type) {
                case 'checkbox':
                    $data[$item->getIdAsInt()] = true;
                    break;
                case 'text':
                case 'select':
                    $data[$item->getIdAsInt()] = 'Test';
                    break;
            }
        }
        $this->handler->handleFormPost($this->form, $data, $this->request);
    }

    public function testHandleFormPostSpam(): void
    {
        $items = $this->form->getItems();
        $data = [];
        foreach ($items as $item) {
            /** @var FormItem $item */
            switch ($item->type) {
                case 'email':
                    $data[$item->getIdAsInt()] = 'test@example.com';
                    break;
                case 'checkbox':
                    $data[$item->getIdAsInt()] = true;
                    break;
                case 'text':
                case 'select':
                    $data[$item->getIdAsInt()] = 'Spam';
                    break;
            }
        }
        $this->handler->handleFormPost($this->form, $data, $this->request);
        self::assertTrue(true);
    }

    public function testIsSpam(): void
    {
        $isSpam = $this->handler->isSpam('Test', ['test', 'Start']);
        self::assertTrue($isSpam);
    }

    public function testIsSpamNoSpam(): void
    {
        $isSpam = $this->handler->isSpam('No Spam', ['test', 'Start']);
        self::assertFalse($isSpam);
    }

    public function testIsSpamEmptyValues(): void
    {
        $isSpam = $this->handler->isSpam('No Spam', []);
        self::assertFalse($isSpam);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new FormMessageHandler();
        $this->form = new Form();
        $this->form->description = 'Test description';
        $this->form->title = 'Test Form';
        $this->form->toAddress = 'noreply@example.com';
        $this->form->create();

        foreach (['email', 'checkbox', 'text', 'select'] as $item) {
            $this->createFormItem($item, $item, $item === 'email', $item === 'email', ['Spam']);
        }

        $this->request = new ServerRequest('POST', '', []);
    }

    private function createFormItem(string $label, string $type, bool $isRequired, bool $isFromAddress, array $spamFilter = []): FormItem
    {
        $formItem = new FormItem();
        $formItem->label = $label;
        $formItem->position = 0;
        $formItem->formId = $this->form->getIdAsInt();
        $formItem->type = $type;
        $formItem->isRequired = $isRequired;
        $formItem->isFromAddress = $isFromAddress;
        $formItem->spamFilter = $spamFilter;
        $formItem->create();

        return $formItem;
    }
}
