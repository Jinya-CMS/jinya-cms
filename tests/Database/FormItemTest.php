<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Tests\DatabaseAwareTestCase;

class FormItemTest extends DatabaseAwareTestCase
{
    private Form $form;

    public function testGetForm(): void
    {
        $formItem = $this->createFormItem();
        $form = $formItem->getForm();
        self::assertEquals($this->form->format(), $form->format());
    }

    private function createFormItem(): FormItem
    {
        $formItem = new FormItem();
        $formItem->label = 'Label';
        $formItem->position = 0;
        $formItem->formId = $this->form->id;
        $formItem->id = 0;

        return $formItem;
    }

    public function testFormat(): void
    {
        $formItem = $this->createFormItem();

        self::assertEquals([
            'type' => 'text',
            'options' => [],
            'spamFilter' => [],
            'label' => 'Label',
            'placeholder' => '',
            'helpText' => '',
            'position' => 0,
            'id' => $formItem->id,
            'isRequired' => false,
            'isFromAddress' => false,
            'isSubject' => false,
        ], $formItem->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = new Form();
        $this->form->description = 'Test description';
        $this->form->title = 'Test Form';
        $this->form->toAddress = 'noreply@example.com';
        $this->form->create();
    }
}
