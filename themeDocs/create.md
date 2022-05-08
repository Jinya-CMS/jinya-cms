---
title: Create a theme
parent: Theming
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
├── LICENSE
├── Preview.jpg
├── profile.phtml
├── README.md
├── segment-page.phtml
├── simple-page.phtml
├── styles
│   ├── frontend.scss
│   └── _variables.scss
└── theme.php
```

The purpose of the files is described below.