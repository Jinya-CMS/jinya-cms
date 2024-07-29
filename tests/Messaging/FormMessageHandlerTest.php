<?php

namespace Jinya\Cms\Messaging;

use Jinya\Cms\Database\Form;
use Jinya\Cms\Database\FormItem;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Theming\Engine;
use Jinya\Router\Extensions\Database\Exceptions\MissingFieldsException;
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
                    $data[$item->id] = 'test@example.com';
                    break;
                case 'checkbox':
                    $data[$item->id] = true;
                    break;
                case 'text':
                case 'select':
                    $data[$item->id] = 'Test';
                    break;
            }
        }
        /** @phpstan-ignore-next-line */
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
                    $data[$item->id] = true;
                    break;
                case 'text':
                case 'select':
                    $data[$item->id] = 'Test';
                    break;
            }
        }
        /** @phpstan-ignore-next-line */
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
                    $data[$item->id] = 'test@example.com';
                    break;
                case 'checkbox':
                    $data[$item->id] = true;
                    break;
                case 'text':
                case 'select':
                    $data[$item->id] = 'Spam';
                    break;
            }
        }
        /** @phpstan-ignore-next-line */
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

        $items = [];
        foreach (['email', 'checkbox', 'text', 'select'] as $item) {
            $items[] = $this->createFormItem($item, $item, $item === 'email', $item === 'email', ['Spam']);
        }
        $this->form->replaceItems($items);

        $this->request = new ServerRequest('POST', '', []);
    }

    /**
     * @param string $label
     * @param string $type
     * @param bool $isRequired
     * @param bool $isFromAddress
     * @param string[] $spamFilter
     * @return array
     */
    private function createFormItem(
        string $label,
        string $type,
        bool $isRequired,
        bool $isFromAddress,
        array $spamFilter = []
    ): array {
        return [
            'label' => $label,
            'position' => 0,
            'formId' => $this->form->id,
            'type' => $type,
            'isRequired' => $isRequired,
            'isFromAddress' => $isFromAddress,
            'spamFilter' => $spamFilter,
        ];
    }
}
