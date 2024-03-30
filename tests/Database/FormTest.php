<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\Form;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use PDOException;

class FormTest extends DatabaseAwareTestCase
{
    public function testFindById(): void
    {
        $form = $this->createForm();
        $foundForm = Form::findById($form->id);

        self::assertEquals($form->id, $foundForm->id);
        self::assertEquals($form->title, $foundForm->title);
    }

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

    public function testFindByIdNotFound(): void
    {
        $foundForm = Form::findById(-1);
        self::assertNull($foundForm);
    }

    public function testFindAll(): void
    {
        $this->createForm();
        $this->createForm(title: 'Test');
        $this->createForm(title: 'Test32');

        self::assertCount(3, iterator_to_array(Form::findAll()));
    }

    public function testFindAllNoneFound(): void
    {
        $forms = Form::findAll();
        self::assertCount(0, iterator_to_array($forms));
    }

    public function testGetUpdatedBy(): void
    {
        $form = $this->createForm();
        $updatedBy = $form->getUpdatedBy();
        self::assertEquals(CurrentUser::$currentUser, $updatedBy);
    }

    public function testDelete(): void
    {
        $form = $this->createForm();
        $form->delete();

        $foundForm = Form::findById($form->id);
        self::assertNull($foundForm);
    }

    public function testDeleteWithItems(): void
    {
        $form = $this->createForm();
        $item = [
            'type' => 'text',
            'formId' => $form->id,
            'label' => 'Label',
        ];

        $form->replaceItems([$item]);
        $form->delete();

        $foundForm = Form::findById($form->id);
        self::assertNull($foundForm);
    }

    public function testDeleteNotExists(): void
    {
        $this->expectError();
        $form = $this->createForm(execute: false);
        $form->delete();
    }

    public function testGetCreator(): void
    {
        $form = $this->createForm();
        $creator = $form->getCreator();
        self::assertEquals(CurrentUser::$currentUser, $creator);
    }

    public function testFormat(): void
    {
        $form = $this->createForm();
        self::assertArrayHasKey('id', $form->format());
        self::assertArrayHasKey('description', $form->format());
        self::assertArrayHasKey('title', $form->format());
        self::assertArrayHasKey('toAddress', $form->format());
        self::assertArrayHasKey('created', $form->format());
    }

    public function testUpdate(): void
    {
        $form = $this->createForm();
        self::assertEquals('Testform', $form->title);

        $form->title = 'Update me';
        $form->description = 'Updated desc';
        $form->toAddress = 'test@example.com';
        $form->update();

        $foundForm = Form::findById($form->id);
        self::assertEquals($form->title, $foundForm->title);
        self::assertEquals($form->description, $foundForm->description);
        self::assertEquals($form->toAddress, $foundForm->toAddress);
    }

    public function testUpdateUniqueFailed(): void
    {
        $this->expectException(PDOException::class);
        $this->createForm(title: 'Test');
        $form = $this->createForm();
        self::assertEquals('Testform', $form->title);

        $form->title = 'Test';
        $form->description = 'Updated desc';
        $form->toAddress = 'test@example.com';
        $form->update();
    }

    public function testGetItems(): void
    {
        $form = $this->createForm();
        $item = [
            'type' => 'text',
            'formId' => $form->id,
            'label' => 'Label',
            'position' => 0,
        ];
        $form->replaceItems([$item]);

        $items = $form->getItems();
        self::assertCount(1, iterator_to_array($items));
    }

    public function testGetItemsMultipleForms(): void
    {
        $form = $this->createForm();
        $item = [
            'type' => 'text',
            'formId' => $form->id,
            'label' => 'Label',
        ];
        $form->replaceItems([$item]);

        $form2 = $this->createForm(title: 'Form2');
        $item2 = [
            'type' => 'text',
            'formId' => $form->id,
            'label' => 'Label',
        ];
        $form2->replaceItems([$item2]);

        $items = $form->getItems();
        self::assertCount(1, iterator_to_array($items));
    }

    public function testCreate(): void
    {
        $form = $this->createForm(execute: false);
        $form->create();

        $foundForm = Form::findById($form->id);
        self::assertNotNull($foundForm);
    }

    public function testCreateUniqueFailed(): void
    {
        $this->expectException(PDOException::class);
        $this->createForm();
        $this->createForm();
    }
}
