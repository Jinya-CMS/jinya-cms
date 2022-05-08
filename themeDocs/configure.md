---
title: Configure with theme.php
parent: Theming
---

# Configuration with theme.php

The file theme.php contains a simple PHP array for its configuration structure. In this array you configure which links
a theme has, the SCSS, JS files to use and the a list of assets that should get copied into the public folder.

This is a simple configuration file, the fields of the array are described below the file.

```php
<?php

return [
    'displayName' => 'Jinya Testing Theme',
    'description' => "A simple theme for testing purposes. This theme has no real frontend so don't use it",
    'previewImage' => __DIR__ . '/Preview.jpg',
    'errorBehavior' => \App\Theming\Theme::ERROR_BEHAVIOR_ERROR_PAGE,
    'styles' => [
        'variables' => __DIR__ . '/styles/_variables.scss',
        'files' => [
            __DIR__ . '/styles/frontend.scss',
        ],
    ],
    'scripts' => [
        __DIR__ . '/scripts/helloworld.js',
    ],
    'assets' => [
        'asset1' => __DIR__ . '/assets/icons/facebook.svg',
    ],
    'configuration' => [
        'configGroup1' => [
            'text' => 'Text value',
            'boolean1' => false,
        ],
    ],
    'configurationStructure' => [
        'title' => 'Configure Jinya testing theme',
        'groups' => [
            [
                'name' => 'configGroup1',
                'title' => 'Config Group 1',
                'fields' => [
                    [
                        'name' => 'text',
                        'type' => 'string',
                        'label' => 'Text value 1',
                    ],
                    [
                        'name' => 'boolean1',
                        'type' => 'boolean',
                        'label' => 'Boolean value 1',
                    ],
                ],
            ],
        ],
        'links' => [
            'blog_categories' => [
                'blogCategory1' => 'Category 1',
            ],
            'files' => [
                'file1' => 'File 1',
            ],
            'forms' => [
                'form1' => 'Form 1',
            ],
            'galleries' => [
                'gallery1' => 'Gallery 1',
            ],
            'segment_pages' => [
                'segmentPage1' => 'Segment Page 1',
            ],
            'menus' => [
                'menu1' => 'Menu 1',
            ],
            'pages' => [
                'page1' => 'Page 1',
            ],
        ],
    ],
];

```

Let's start with the simple fields, see the table below:

| Field           | Purpose                                                                                      | Type   | Notes                                                    |
|-----------------|----------------------------------------------------------------------------------------------|--------|----------------------------------------------------------|
| `displayName`   | This name is used to show your themes name in the designer                                   | string | -                                                        |
| `description`   | The description is a detailed text about your theme                                          | string | May contain HTML                                         |
| `previewImage`  | Contains the absolute path to the preview image of your theme.                               | string | It is recommended to use the `__DIR__` super global      |
| `errorBehavior` | The behavior your theme has when an error occurs, it can either be `errorpage` or `homepage` | string | The `Theme` class has constants prepared for error pages |

## Specifying SCSS stylesheets

The theme.php contains a simple way to specify the stylesheets you want to use. At the moment only one output stylesheet
will be generated. Apart from that it is also possible to customize SCSS variables in the designer. To achieve that,
Jinya analyses the specified variables file and then displays the variables in the designer. The array key used is
styles.

The structure for the styling is simple and shown below:

```php
'styles' => [
    'variables' => __DIR__ . '/styles/_variables.scss',
    'files' => [
        __DIR__ . '/styles/frontend.scss',
    ],
]
```

The array key `variables` contains the absolute path to the SCSS file you use to define your variables. It is
recommended to have a single file containing all SCSS variables, so artists can change them in the designer. Jinya
parses the variables based on the `!default` attribute for SCSS.

Check this snippet to see how variables should be declared.

```scss
$primary-color: #ff0000 !default;
```

The `files` array just contains a list of absolute paths you want to compile. During compilation, SCSS imported files
will be resolved, based on the directory containing the SCSS file.

## Specifying JS files

Defining JS file is rather simple, the `scripts` field contains a list of absolute paths that point to JS files.

## Specifying assets

It is also possible to define arbitrary assets. The main intention was to include fonts in your SCSS stylesheets. Most
assets can be included with links, which are defined below. You define assets in key-value pairs, the key is the name
and the value is the absolute path to the asset.

You can reference an asset in your SCSS with the `jinya-asset` function. Simply pass it the asset name, it will be
replaced with a URL referencing the asset.

## Specifying the default configuration

The default configuration is specified with the `configuration` key. A simple sample looks as follows.

```php
'configuration' => [
    'configGroup1' => [
        'text' => 'Text value',
    ],
    'configGroup2' => [
        'text1' => 'Text value',
        'text2' => 'Text value',
        'boolean1' => false,
    ],
],
```

Every configuration group contains of a key and an array containing the configurations keys and the default values. As
the time of writing Jinya supports booleans and single line strings, it is planned to extend it to multiline strings as
well.

## Specifying the configuration structure and links

The configuration structure and the links are configured in the array key `configurationStructure`. The key contains two
nested arrays, one named groups the other one named links.

### Specifying the configuration structure

Specifying the configuration structure is rather easy. The structure needs to follow the default configuration. It is
basically an array of groups containing fields. Check the configuration structure below.

```php
'groups' => [
    [
        'name' => 'configGroup1',
        'title' => 'Config Group 1',
        'fields' => [
            [
                'name' => 'text',
                'type' => 'string',
                'label' => 'Text value 1',
            ],
        ],
    ],
    [
        'name' => 'configGroup2',
        'title' => 'Config Group 2',
        'fields' => [
            [
                'name' => 'text1',
                'type' => 'string',
                'label' => 'Text value 1',
            ],
            [
                'name' => 'text2',
                'type' => 'string',
                'label' => 'Text value 2',
            ],
            [
                'name' => 'boolean1',
                'type' => 'boolean',
                'label' => 'Boolean value 1',
            ],
        ],
    ],
],
```

Every group needs to contain the groups name, specified in the default configuration. Since the group names are rather
technical, you also need to specify a `title` attribute. The title will be displayed in the designer above the fields.

To allow artists to change the configuration you also specify a list of fields. A field contains a `name`, a `type` and
a`label`. The name has to match the name in the default configuration. The type can either be `string` or `boolean`.

### Specifying the links

It is possible to link several objects. At the moment, segment pages, menus, simple pages, forms, galleries, files and
blog categories are supported.

The different objects are described in the table below.

| Object type   | Configuration key |
|---------------|-------------------|
| Blog Category | blog_categories   |
| File          | file              |
| Form          | forms             |
| Gallery       | galleries         |
| Menu          | menus             |
| Segment Page  | segment_pages     |
| Simple page   | pages             |

Each of these keys contains an array of key-value pairs. The key is used for reference in your themes, the value on the
other hand is used as the label in the designer. You can find a sample below:

```php
'links' => [
    'blog_categories' => [
        'blogCategory1' => 'Category 1',
    ],
    'files' => [
        'file1' => 'File 1',
    ],
    'forms' => [
        'form1' => 'Form 1',
    ],
    'galleries' => [
        'gallery1' => 'Gallery 1',
    ],
    'segment_pages' => [
        'segmentPage1' => 'Segment Page 1',
    ],
    'menus' => [
        'menu1' => 'Menu 1',
    ],
    'pages' => [
        'page1' => 'Page 1',
    ],
],
```