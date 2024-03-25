<?php

namespace Jinya\Tests\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\Tests\DatabaseAwareTestCase;
use RuntimeException;

class FormItemTest extends DatabaseAwareTestCase
{
    private Form $form;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = new Form();
        $this->form->description = 'Test description';
        $this->form->title = 'Test Form';
        $this->form->toAddress = 'noreply@example.com';
        $this->form->create();
    }

    private function createFormItem(int $position = 0, string $label = 'Label', bool $execute = true): FormItem
    {
        $formItem = new FormItem();
        $formItem->label = $label;
        $formItem->position = $position;
        $formItem->formId = $this->form->id;
        $formItem->id = 0;

        return $formItem;
    }

    public function testGetForm(): void
    {
        $formItem = $this->createFormItem();
        $form = $formItem->getForm();
        $this->assertEquals($this->form->format(), $form->format());
    }

    public function testFormat(): void
    {
        $formItem = $this->createFormItem();

        $this->assertEquals([
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
}
