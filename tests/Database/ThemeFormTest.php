<?php

namespace Jinya\Tests\Database;

use App\Database\Form;
use App\Database\ThemeForm;
use App\Tests\ThemeTestCase;
use Faker\Provider\Uuid;

class ThemeFormTest extends ThemeTestCase
{
    private Form $form;

    public function testFindByThemeNoForm(): void
    {
        $themeForms = ThemeForm::findByTheme($this->theme->getIdAsInt());
        self::assertCount(0, $themeForms);
    }

    public function testFindByTheme(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->getIdAsInt();
        $themeForm->themeId = $this->theme->getIdAsInt();
        $themeForm->name = 'Test';
        $themeForm->create();

        $themeForms = ThemeForm::findByTheme($this->theme->getIdAsInt());
        self::assertCount(1, $themeForms);
    }

    public function testFindByThemeAndNameNotFound(): void
    {
        $found = ThemeForm::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNull($found);
    }

    public function testFindByThemeAndName(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->getIdAsInt();
        $themeForm->themeId = $this->theme->getIdAsInt();
        $themeForm->name = 'Test';
        $themeForm->create();

        $found = ThemeForm::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertEquals($themeForm->format(), $found->format());
    }

    public function testDelete(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->getIdAsInt();
        $themeForm->themeId = $this->theme->getIdAsInt();
        $themeForm->name = 'Test';
        $themeForm->create();

        self::assertNotNull(ThemeForm::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));

        $themeForm->delete();
        self::assertNull(ThemeForm::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testUpdate(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->getIdAsInt();
        $themeForm->themeId = $this->theme->getIdAsInt();
        $themeForm->name = 'Test';
        $themeForm->create();

        $form = new Form();
        $form->title = 'Tempform';
        $form->toAddress = 'example@example.com';
        $form->create();

        $themeForm->formId = $form->getIdAsInt();
        $themeForm->update();
        $found = ThemeForm::findByThemeAndName($this->theme->getIdAsInt(), 'Test');
        self::assertNotNull($found);
        self::assertEquals($form->getIdAsInt(), $found->formId);
    }

    public function testCreate(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->getIdAsInt();
        $themeForm->themeId = $this->theme->getIdAsInt();
        $themeForm->name = 'Test';
        $themeForm->create();

        self::assertNotNull(ThemeForm::findByThemeAndName($this->theme->getIdAsInt(), 'Test'));
    }

    public function testFormat(): void
    {
        $themeForm = new ThemeForm();
        $themeForm->formId = $this->form->getIdAsInt();
        $themeForm->themeId = $this->theme->getIdAsInt();
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
