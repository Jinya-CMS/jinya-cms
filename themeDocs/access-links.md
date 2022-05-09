---
title: Access theme configuration
parent: Theming
nav_order: 2
---

# Access theme links

Accessing theme links is rather simple. Every view gets passed methods to check if a link is set and a method to get the
linked object directly from the database.

## Blog categories

To check if a theme has a linked blog category set, simple call the method `$this->hasBlogCategory('key')`. This will
return true if the links is set and false if not.

After you checked whether the link exists, you can grab it with `$this->blogCategory('key')`. The return type is an
instance of the BlogCategory class.

Check the code below to see a full example:

```php
<?php
    if ($this->hasBlogCategory('key')) {
        var_dump($this->blogCategory('key'));
    }
?>
```

## File

To check if a theme has a linked file set, simple call the method `$this->hasFile('key')`. This will return true if the
links is set and false if not.

After you checked whether the link exists, you can grab it with `$this->file('key')`. The return type is an instance of
the File class.

Check the code below to see a full example:

```php
<?php
    if ($this->hasFile('key')) {
        var_dump($this->file('key'));
    }
?>
```

## Forms

To check if a theme has a linked form set, simple call the method `$this->hasForm('key')`. This will return true if the
links is set and false if not.

After you checked whether the link exists, you can grab it with `$this->form('key')`. The return type is an instance of
the Form class.

Check the code below to see a full example:

```php
<?php
    if ($this->hasForm('key')) {
        var_dump($this->form('key'));
    }
?>
```

## Galleries

To check if a theme has a linked gallery set, simple call the method `$this->hasGallery('key')`. This will return true
if the links is set and false if not.

After you checked whether the link exists, you can grab it with `$this->gallery('key')`. The return type is an instance
of the Gallery class.

Check the code below to see a full example:

```php
<?php
    if ($this->hasGallery('key')) {
        var_dump($this->gallery('key'));
    }
?>
```

## Menus

To check if a theme has a linked menu set, simple call the method `$this->hasMenu('key')`. This will return true if the
links is set and false if not.

After you checked whether the link exists, you can grab it with `$this->menu('key')`. The return type is an instance
of the Menu class.

Check the code below to see a full example:

```php
<?php
    if ($this->hasMenu('key')) {
        var_dump($this->menu('key'));
    }
?>
```

## Simple page

To check if a theme has a linked simple page set, simple call the method `$this->hasSimplePage('key')`. This will return
true if the links is set and false if not.

After you checked whether the link exists, you can grab it with `$this->simplePage('key')`. The return type is an
instance of the SimplePage class.

Check the code below to see a full example:

```php
<?php
    if ($this->hasSimplePage('key')) {
        var_dump($this->simplePage('key'));
    }
?>
```

## Segment page

To check if a theme has a linked segment page set, simple call the method `$this->hasSegmentPage('key')`. This will
return
true if the links is set and false if not.

After you checked whether the link exists, you can grab it with `$this->simplePage('key')`. The return type is an
instance of the SegmentPage class.

Check the code below to see a full example:

```php
<?php
    if ($this->hasSegmentPage('key')) {
        var_dump($this->simplePage('key'));
    }
?>
```
