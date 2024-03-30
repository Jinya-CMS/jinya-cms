<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Database\Form;
use Jinya\Cms\Database\ThemeForm;
use Jinya\Cms\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeFormTest extends ThemeTestCase
{
    private Form $form;

    public function testFindByThemeNoForm(): void
    {
        $themeForms = ThemeForm::findByTheme($this->theme->id);
        self::assertCount(0, iterator_to_array($themeForms));
    }

    public function testFindByTheme(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->id;
        $themeForm->themeId = $this->theme->id;
        $themeForm->name = 'Test';
        $themeForm->create();

        $themeForms = ThemeForm::findByTheme($this->theme->id);
        self::assertCount(1, iterator_to_array($themeForms));
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeForm::findByThemeAndName($this->theme->id, 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->id;
        $themeForm->themeId = $this->theme->id;
        $themeForm->name = 'Test';
        $themeForm->create();

        $found = ThemeForm::findByThemeAndName($this->theme->id, 'Test');
        self::assertEquals($themeForm->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->id;
        $themeForm->themeId = $this->theme->id;
        $themeForm->name = 'Test';
        $themeForm->create();

        self::assertNotNull(ThemeForm::findByThemeAndName($this->theme->id, 'Test'));

        $themeForm->delete();
        self::assertNull(ThemeForm::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testUpdate(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->id;
        $themeForm->themeId = $this->theme->id;
        $themeForm->name = 'Test';
        $themeForm->create();

        $form = new Form();
        $form->title = 'Tempform';
        $form->toAddress = 'example@example.com';
        $form->create();

        $themeForm->formId = $form->id;
        $themeForm->update();
        $found = ThemeForm::findByThemeAndName($this->theme->id, 'Test');
        self::assertNotNull($found);
        self::assertEquals($form->id, $found->formId);
    }

    public function testCreate(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->id;
        $themeForm->themeId = $this->theme->id;
        $themeForm->name = 'Test';
        $themeForm->create();

        self::assertNotNull(ThemeForm::findByThemeAndName($this->theme->id, 'Test'));
    }

    public function testFormat(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->id;
        $themeForm->themeId = $this->theme->id;
        $themeForm->name = 'Test';
        $themeForm->create();

        self::assertEquals([
            'name' => 'Test',
            'form' => $this->form->format(),
        ], $themeForm->format());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'example@example.com';
        $form->create();

        $this->form = $form;
    }
}
