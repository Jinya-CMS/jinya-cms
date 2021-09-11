<?php

return [
    'displayName' => 'Jinya Default Theme',
    'description' => 'The default theme of the jinya cms is modern and provides a good looking user interface.',
    'previewImage' => __DIR__ . '/Preview.png',
    'styles' => [
        'variables' => __DIR__ . '/styles/_variables.scss',
        'files' => [
            __DIR__ . '/styles/frontend.scss',
        ],
    ],
    'scripts' => [
        __DIR__ . '/scripts/menu.dist.js',
        __DIR__ . '/scripts/menu-mobile.dist.js',
        __DIR__ . '/scripts/scrollhelper.dist.js',
        __DIR__ . '/scripts/gallery-masonry.dist.js',
    ],
    'configuration' => [
        'page' => [
            'title' => 'Jinya CMS',
        ],
        'footer' => [
            'copyright' => null,
        ],
        'fonts' => [
            'menu' => 'https://fonts.googleapis.com/css?family=Open+Sans',
            'heading' => 'https://fonts.googleapis.com/css?family=Raleway:300,400',
            'paragraph' => 'https://fonts.googleapis.com/css?family=Open+Sans',
            'brand' => 'https://fonts.googleapis.com/css?family=Josefin+Sans',
        ],
        'input' => [
            'optional' => ' (optional)',
        ],
        'buttons' => [
            'submit' => 'Submit',
        ],
        'dropdowns' => [
            'placeholder' => 'Please choose...',
        ],
        'messages' => [
            'mail_sent_message' => 'Mail was sent successfully',
            'mail_not_sent_message' => 'Mail could not be sent',
        ],
        'profile' => [
            'show_email' => false,
        ],
        'blog' => [
            'title' => 'Blog'
        ],
    ],
    'configurationStructure' => [
        'title' => 'Configure Jinya default theme',
        'groups' => [
            [
                'name' => 'page',
                'title' => 'Page',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'string',
                        'label' => 'Title',
                    ],
                ],
            ],
            [
                'name' => 'footer',
                'title' => 'Footer',
                'fields' => [
                    [
                        'name' => 'copyright',
                        'type' => 'string',
                        'label' => 'Copyright message',
                    ],
                ],
            ],
            [
                'name' => 'fonts',
                'title' => 'Font links',
                'fields' => [
                    [
                        'name' => 'menu',
                        'type' => 'string',
                        'label' => 'Menu entries',
                    ],
                    [
                        'name' => 'heading',
                        'type' => 'string',
                        'label' => 'Headings',
                    ],
                    [
                        'name' => 'paragraph',
                        'type' => 'string',
                        'label' => 'Text',
                    ],
                    [
                        'name' => 'brand',
                        'type' => 'string',
                        'label' => 'Branding',
                    ],
                ],
            ],
            [
                'name' => 'input',
                'title' => 'Input field options',
                'fields' => [
                    [
                        'name' => 'optional',
                        'type' => 'string',
                        'label' => 'Mark optional fields',
                    ],
                ],
            ],
            [
                'name' => 'buttons',
                'title' => 'Button labels',
                'fields' => [
                    [
                        'name' => 'submit',
                        'type' => 'string',
                        'label' => 'Submit',
                    ],
                ],
            ],
            [
                'name' => 'dropdowns',
                'title' => 'Dropdowns',
                'fields' => [
                    [
                        'name' => 'placeholder',
                        'type' => 'string',
                        'label' => 'Placeholder',
                    ],
                ],
            ],
            [
                'name' => 'messages',
                'title' => 'Messages',
                'fields' => [
                    [
                        'name' => 'mail_sent_message',
                        'type' => 'string',
                        'label' => 'Mail sent message',
                    ],
                    [
                        'name' => 'mail_not_sent_message',
                        'type' => 'string',
                        'label' => 'Mail not sent message',
                    ],
                ],
            ],
            [
                'name' => 'profile',
                'title' => 'Profile pages',
                'fields' => [
                    [
                        'name' => 'show_email',
                        'type' => 'boolean',
                        'label' => 'Show mail address',
                    ],
                ],
            ],
            [
                'name' => 'blog',
                'title' => 'Blog',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'string',
                        'label' => 'Blog home page title',
                    ],
                ],
            ],
        ],
        'links' => [
            'segment_pages' => [
                'startpage' => 'Homepage',
            ],
            'menus' => [
                'primary' => 'Main menu',
                'footer' => 'Footer menu',
                'shadow' => 'Invisible links',
            ],
            'files' => [
                'faviconSmall' => 'Small favicon 64x64',
                'faviconShortcutIcon' => 'Small favicon in ico file format',
                'faviconLarge' => 'Big favicon 512x512',
            ],
        ],
    ],
];
