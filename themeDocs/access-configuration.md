---
title: Access theme links
parent: Theming
nav_order: 3
---

# Access theme configuration

Accessing the theme configuration is rather simple. Every view gets passed an instance method to access configuration
values. A simple code sample is shown below:

```php
<?= $this->config('group_name', 'config_name') ?>
```

This small code snippet will return either a string or a boolean, based on the type you configured in your theme.php. If
you access a configuration you have not set in your theme.php a PHP warning is output.