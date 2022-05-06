<?php

namespace Jinya\Tests\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Form;
use App\Database\FormItem;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FormItemTest extends TestCase
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
        $formItem->formId = $this->form->getIdAsInt();
        if ($execute) {
            $formItem->create();
        }

        return $formItem;
    }

    public function testGetForm(): void
    {
        $formItem = $this->createFormItem();
        $form = $formItem->getForm();
        $this->assertEquals($this->form->format(), $form->format());
    }

    public function testFindById(): void
    {
        $this->expectException(RuntimeException::class);
        FormItem::findById(1);
    }

    public function testFindAll(): void
    {
        $this->expectException(RuntimeException::class);
        FormItem::findAll();
    }

    public function testUpdate(): void
    {
        $formItem = $this->createFormItem();
        $formItem->label = 'Testlabel';
        $formItem->update();

        $savedItem = FormItem::findByPosition($this->form->getIdAsInt(), $formItem->position);
        $this->assertEquals($formItem->label, $savedItem->label);
    }

    public function testUpdateNonExistent(): void
    {
        $formItem = $this->createFormItem();
        $formItem->label = 'Testlabel';
        $formItem->delete();
        $formItem->update();

        $savedItem = FormItem::findByPosition($this->form->getIdAsInt(), $formItem->position);
        $this->assertNull($savedItem);
    }

    public function testMove(): void
    {
        $this->createFormItem(0, label: '1');
        $this->createFormItem(1, label: '2');
        $this->createFormItem(2, label: '3');
        $formItem = $this->createFormItem(3, label: '4');

        $formItem->move(1);
        $items = iterator_to_array($this->form->getItems());
        $this->assertEquals('1', $items[0]->label);
        $this->assertEquals('4', $items[1]->label);
        $this->assertEquals('2', $items[2]->label);
        $this->assertEquals('3', $items[3]->label);
    }

    public function testMoveNotExistent(): void
    {
        $this->createFormItem(1, label: '1');
        $this->createFormItem(2, label: '2');
        $this->createFormItem(3, label: '3');
        $formItem = $this->createFormItem(4, label: '4', execute: false);

        $formItem->move(2);
        $items = iterator_to_array($this->form->getItems());
        $this->assertEquals('1', $items[0]->label);
        $this->assertEquals('2', $items[1]->label);
        $this->assertEquals('3', $items[2]->label);
    }

    public function testCreate(): void
    {
        $formItem = $this->createFormItem(execute: false);
        $formItem->create();
        $this->assertTrue(true);
    }

    public function testCreateFormNotExistent(): void
    {
        $this->expectException(ForeignKeyFailedException::class);
        $formItem = $this->createFormItem(execute: false);
        $formItem->formId = -1;
        $formItem->create();
    }

    public function testFindByKeyword(): void
    {
        $this->expectException(RuntimeException::class);
        FormItem::findByKeyword('1');
    }

    public function testFindByPosition(): void
    {
        $this->createFormItem(0);
        $this->createFormItem(1);
        $this->createFormItem(2);
        $formItem = $this->createFormItem(3);

        $foundItem = FormItem::findByPosition($this->form->getIdAsInt(), 3);
        $this->assertEquals($formItem, $foundItem);
    }

    public function testFindByPositionNotExisting(): void
    {
        $this->createFormItem(0);
        $this->createFormItem(1);
        $this->createFormItem(2);
        $this->createFormItem(3);

        $foundItem = FormItem::findByPosition($this->form->getIdAsInt(), 5);
        $this->assertNull($foundItem);
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
            'id' => $formItem->getIdAsInt(),
            'isRequired' => false,
            'isFromAddress' => false,
            'isSubject' => false,
        ], $formItem->format());
    }

    public function testDelete(): void
    {
        $formItem = $this->createFormItem();
        $formItem->delete();

        $savedItem = FormItem::findByPosition($this->form->getIdAsInt(), $formItem->position);
        $this->assertNull($savedItem);
    }

    public function testDeleteNotExistent(): void
    {
        $formItem = $this->createFormItem();
        $formItem->delete();
        $formItem->delete();

        $savedItem = FormItem::findByPosition($this->form->getIdAsInt(), $formItem->position);
        $this->assertNull($savedItem);
    }
}
