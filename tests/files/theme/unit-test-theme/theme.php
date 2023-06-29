<?php

return [
    'displayName' => 'Jinya Testing Theme',
    'description' => "A simple theme for testing purposes. This theme has no real frontend so don't use it",
    'previewImage' => __DIR__ . '/Preview.jpg',
    'hasApi' => true,
    'errorBehavior' => \App\Theming\Theme::ERROR_BEHAVIOR_ERROR_PAGE,
    'styles' => [
        'variables' => __DIR__ . '/styles/_variables.scss',
        'files' => [
            __DIR__ . '/styles/frontend.scss',
            __DIR__ . '/styles/does/not/exist.scss'
        ],
    ],
    'assets' => [
        'poppins400Regular' => __DIR__ . '/assets/fonts/Poppins.latin.regular.woff2',
    ],
    'scripts' => [
        __DIR__ . '/scripts/helloworld.js',
        __DIR__ . '/scripts/does/not/exist.js'
    ],
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
        'links' => [
            'segment_pages' => [
                'segmentPage1' => 'Segment Page 1',
                'segmentPage2' => 'Segment Page 2',
                'segmentPage3' => 'Segment Page 3',
            ],
            'menus' => [
                'menu1' => 'Menu 1',
                'menu2' => 'Menu 2',
                'menu3' => 'Menu 3',
            ],
            'pages' => [
                'page1' => 'Page 1',
                'page2' => 'Page 2',
                'page3' => 'Page 3',
            ],
            'forms' => [
                'form1' => 'Form 1',
                'form2' => 'Form 2',
                'form3' => 'Form 3',
            ],
            'galleries' => [
                'gallery1' => 'Gallery 1',
                'gallery2' => 'Gallery 2',
                'gallery3' => 'Gallery 3',
            ],
            'files' => [
                'file1' => 'File 1',
                'file2' => 'File 2',
                'file3' => 'File 3',
            ],
            'blog_categories' => [
                'blogCategory1' => 'Category 1',
                'blogCategory2' => 'Category 2',
                'blogCategory3' => 'Category 3',
            ],
        ],
    ],
];
