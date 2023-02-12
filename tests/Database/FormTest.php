<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\Tests\DatabaseAwareTestCase;

class FormTest extends DatabaseAwareTestCase
{
    private function createForm(string $title = 'Testform', bool $execute = true): Form
    {
        $form = new Form();
        $form->description = 'Test description';
        $form->title = $title;
        $form->toAddress = 'noreply@example.com';

        if ($execute) {
            $form->create();
        }

        return $form;
    }

    public function testFindById(): void
    {
        $form = $this->createForm();
        $foundForm = Form::findById($form->getIdAsInt());

        $this->assertEquals($form->id, $foundForm->id);
        $this->assertEquals($form->title, $foundForm->title);
    }

    public function testFindByIdNotFound(): void
    {
        $foundForm = Form::findById(-1);
        $this->assertNull($foundForm);
    }

    public function testFindAll(): void
    {
        $this->createForm();
        $this->createForm(title: 'Test');
        $this->createForm(title: 'Test32');

        $this->assertCount(3, iterator_to_array(Form::findAll()));
    }

    public function testFindAllNoneFound(): void
    {
        $forms = Form::findAll();
        $this->assertCount(0, iterator_to_array($forms));
    }

    public function testGetUpdatedBy(): void
    {
        $form = $this->createForm();
        $updatedBy = $form->getUpdatedBy();
        $this->assertEquals(CurrentUser::$currentUser, $updatedBy);
    }

    public function testDelete(): void
    {
        $form = $this->createForm();
        $form->delete();

        $foundForm = Form::findById($form->getIdAsInt());
        $this->assertNull($foundForm);
    }

    public function testDeleteWithItems(): void
    {
        $form = $this->createForm();
        $item = new FormItem();
        $item->type = 'text';
        $item->formId = $form->getIdAsInt();
        $item->label = 'Label';
        $item->position = 0;
        $item->create();
        $form->delete();

        $foundForm = Form::findById($form->getIdAsInt());
        $this->assertNull($foundForm);
    }

    public function testDeleteNotExists(): void
    {
        $form = $this->createForm(execute: false);
        $form->delete();

        $foundForm = Form::findById($form->getIdAsInt());
        $this->assertNull($foundForm);
    }

    public function testGetCreator(): void
    {
        $form = $this->createForm();
        $creator = $form->getCreator();
        $this->assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testFormat(): void
    {
        $form = $this->createForm();
        $this->assertArrayHasKey('id', $form->format());
        $this->assertArrayHasKey('description', $form->format());
        $this->assertArrayHasKey('title', $form->format());
        $this->assertArrayHasKey('toAddress', $form->format());
        $this->assertArrayHasKey('created', $form->format());
    }

    public function testUpdate(): void
    {
        $form = $this->createForm();
        $this->assertEquals('Testform', $form->title);

        $form->title = 'Update me';
        $form->description = 'Updated desc';
        $form->toAddress = 'test@example.com';
        $form->update();

        $foundForm = Form::findById($form->getIdAsInt());
        $this->assertEquals($form->title, $foundForm->title);
        $this->assertEquals($form->description, $foundForm->description);
        $this->assertEquals($form->toAddress, $foundForm->toAddress);
    }

    public function testUpdateUniqueFailed(): void
    {
        $this->expectException(UniqueFailedException::class);
        $this->createForm(title: 'Test');
        $form = $this->createForm();
        $this->assertEquals('Testform', $form->title);

        $form->title = 'Test';
        $form->description = 'Updated desc';
        $form->toAddress = 'test@example.com';
        $form->update();
    }

    public function testUpdateNotSaved(): void
    {
        $this->expectError();
        $form = $this->createForm(execute: false);
        $this->assertEquals('Testform', $form->title);

        $form->title = 'Update me';
        $form->description = 'Updated desc';
        $form->toAddress = 'test@example.com';
        $form->update();

        Form::findById($form->getIdAsInt());
    }

    public function testFindByKeyword(): void
    {
        $this->createForm();
        $this->createForm(title: 'Formular');
        $this->createForm(title: 'Test32');

        $this->assertCount(2, iterator_to_array(Form::findByKeyword('Form')));
    }

    public function testGetItems(): void
    {
        $form = $this->createForm();
        $item = new FormItem();
        $item->type = 'text';
        $item->formId = $form->getIdAsInt();
        $item->label = 'Label';
        $item->position = 0;
        $item->create();

        $items = $form->getItems();
        $this->assertCount(1, iterator_to_array($items));
    }

    public function testGetItemsMultipleForms(): void
    {
        $form = $this->createForm();
        $item = new FormItem();
        $item->type = 'text';
        $item->formId = $form->getIdAsInt();
        $item->label = 'Label';
        $item->position = 0;
        $item->create();

        $form2 = $this->createForm(title: 'Form2');
        $item2 = new FormItem();
        $item2->type = 'text';
        $item2->formId = $form2->getIdAsInt();
        $item2->label = 'Label';
        $item2->position = 0;
        $item2->create();

        $items = $form->getItems();
        $this->assertCount(1, iterator_to_array($items));
    }

    public function testCreate(): void
    {
        $form = $this->createForm(execute: false);
        $form->create();

        $foundForm = Form::findById($form->getIdAsInt());
        $this->assertNotNull($foundForm);
    }

    public function testCreateUniqueFailed(): void
    {
        $this->expectException(UniqueFailedException::class);
        $this->createForm();
        $this->createForm();
    }
}
