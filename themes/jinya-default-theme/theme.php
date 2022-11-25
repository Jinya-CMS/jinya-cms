<?php

return [
    'displayName' => 'Jinya Default Theme',
    'description' => [
        'en' => 'The default theme of the jinya cms is modern and provides a good looking user interface.',
        'de' => 'Das Standardtheme vom Jinya CMS. Es bietet eine modern und optisch ansprechende Oberfläche.'
    ],
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
            'menu' => 'https://fonts.jinya.de/css2?family=Open+Sans',
            'heading' => 'https://fonts.jinya.de/css2?family=Raleway:wght@300;400',
            'paragraph' => 'https://fonts.jinya.de/css2?family=Open+Sans',
            'brand' => 'https://fonts.jinya.de/css2?family=Josefin+Sans',
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
        'groups' => [
            [
                'name' => 'page',
                'title' => [
                    'en' => 'Page',
                    'de' => 'Seite',
                ],
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Title',
                            'de' => 'Titel',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'footer',
                'title' => [
                    'en' => 'Footer',
                    'de' => 'Fußzeile',
                ],
                'fields' => [
                    [
                        'name' => 'copyright',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Copyright message',
                            'de' => 'Copyright Text'
                        ],
                    ],
                ],
            ],
            [
                'name' => 'fonts',
                'title' => [
                    'en' => 'Font links',
                    'de' => 'Links zu Schriften',
                ],
                'fields' => [
                    [
                        'name' => 'menu',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Menu entries',
                            'de' => 'Menüeinträge',
                        ],
                    ],
                    [
                        'name' => 'heading',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Headings',
                            'de' => 'Überschriften',
                        ]
                    ],
                    [
                        'name' => 'paragraph',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Paragraphs',
                            'de' => 'Fließtext',
                        ],
                    ],
                    [
                        'name' => 'brand',
                        'type' => 'string',
                        'label' => [
                            'de' => 'Branding',
                            'en' => 'Branding',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'input',
                'title' => [
                    'en' => 'Input field options',
                    'de' => 'Eingabefelderoptionen'
                ],
                'fields' => [
                    [
                        'name' => 'optional',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Mark for optional fields',
                            'de' => 'Marker für optionale Felder',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'buttons',
                'title' => [
                    'en' => 'Button labels',
                    'de' => 'Buttonbeschriftungen',
                ],
                'fields' => [
                    [
                        'name' => 'submit',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Submit',
                            'de' => 'Abschicken',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'dropdowns',
                'title' => [
                    'de' => 'Dropdowns',
                    'en' => 'Dropdowns',
                ],
                'fields' => [
                    [
                        'name' => 'placeholder',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Placeholder',
                            'de' => 'Platzhalter',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'messages',
                'title' => [
                    'en' => 'Messages',
                    'de' => 'Nachrichten',
                ],
                'fields' => [
                    [
                        'name' => 'mail_sent_message',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Mail sent message',
                            'de' => 'Email gesendet',
                        ],
                    ],
                    [
                        'name' => 'mail_not_sent_message',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Mail not sent message',
                            'de' => 'Email nicht gesendet',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'profile',
                'title' => [
                    'en' => 'Profile pages',
                    'de' => 'Profilseiten',
                ],
                'fields' => [
                    [
                        'name' => 'show_email',
                        'type' => 'boolean',
                        'label' => [
                            'en' => 'Show mail address',
                            'de' => 'Emailadressen anzeigen',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'blog',
                'title' => [
                    'de' => 'Blog',
                    'en' => 'Blog',
                ],
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'string',
                        'label' => [
                            'en' => 'Blog home page title',
                            'de' => 'Titel der Blogstartseite',
                        ],
                    ],
                ],
            ],
        ],
        'links' => [
            'segment_pages' => [
                'startpage' => [
                    'en' => 'Homepage',
                    'de' => 'Startseite',
                ],
            ],
            'menus' => [
                'primary' => [
                    'en' => 'Main menu',
                    'de' => 'Hauptmenü',
                ],
                'footer' => [
                    'en' => 'Footer menu',
                    'de' => 'Footermenü',
                ],
                'shadow' => [
                    'en' => 'Invisible links',
                    'de' => 'Nicht sichtbare Links',
                ],
            ],
            'files' => [
                'faviconSmall' => [
                    'en' => 'Small favicon 64x64',
                    'de' => 'Kleines favicon 64x64',
                ],
                'faviconShortcutIcon' => [
                    'en' => 'Favicon in ico file format',
                    'de' => 'Favicon im ICO Datei format',
                ],
                'faviconLarge' => [
                    'en' => 'Big favicon 512x512',
                    'de' => 'Großes Favicon 512x512',
                ],
            ],
        ],
    ],
];
