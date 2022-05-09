---
title: Create a theme
parent: Theming
nav_order: 0
---

# Creating a theme

The easiest way to create a new theme is copying the code
from [jinya-testing-theme](https://github.com/Jinya-CMS/jinya-testing-theme/). It contains all needed files.

## Creating by hand

You can also create the structure by hand. To support all features of Jinya you need to create the following file
structure:

```
your-theme-name
├── 404.phtml
├── blog-category.phtml
├── blog-home-page.phtml
├── blog-post.phtml
├── form.phtml
├── gallery.phtml
├── home.phtml
├── layout.phtml
├── profile.phtml
├── segment-page.phtml
├── simple-page.phtml
├── LICENSE
├── Preview.jpg
├── theme.php
└── styles
    ├── frontend.scss
    └── _variables.scss
```

## Purpose of the files

The different files are used for different types of content.

The file `404.phtml` is used to display error messages.

The file `blog-category.phtml` is used to display blog categories.

The file `blog-home-page.phtml` is used to display the blog homepage.

The file `blog-post.phtml` is used to display blog posts.

The file `form.phtml` is used to display forms.

The file `gallery.phtml` is used to display galleries.

The file `home.phtml` is used to display the home page.

The file `layout.phtml` is used to display as a the base layout for all other pages.

The file `profile.phtml` is used to display artist profiles.

The file `segment-page.phtml` is used to display segment pages.

The file `simple-page.phtml` is used to display simple pages.

The file `LICENSE` is used to specify the license of your code, it is completely optional.

The file `Preview.jpg` is used to show a preview image in the designer.

The file `theme.php` is used to [configure the theme](configure.md).

The folder styles contains all your styling files. It is required to have a file `_variables.scss`. You also need at
least one SCSS file to contain your style rules. It is recommended to name this file frontend.scss.

If you want to know more about the files needed for a theme, checkout
the [Jinya Testing Theme](https://github.com/Jinya-CMS/jinya-testing-theme), as it contains all files
with a description.