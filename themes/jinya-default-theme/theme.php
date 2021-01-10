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
    ],
    'configurationStructure' => [
        'title' => 'Jinya default Theme konfigurieren',
        'groups' => [
            [
                'name' => 'page',
                'title' => 'Seite',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'string',
                        'label' => 'Titel',
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
                        'label' => 'Copyright Nachricht',
                    ],
                ],
            ],
            [
                'name' => 'fonts',
                'title' => 'Fontlinks',
                'fields' => [
                    [
                        'name' => 'menu',
                        'type' => 'string',
                        'label' => 'Menueinträge',
                    ],
                    [
                        'name' => 'heading',
                        'type' => 'string',
                        'label' => 'Überschriften',
                    ],
                    [
                        'name' => 'paragraph',
                        'type' => 'string',
                        'label' => 'Fließtext',
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
                'title' => 'Eingabefeldoptionen',
                'fields' => [
                    [
                        'name' => 'optional',
                        'type' => 'string',
                        'label' => 'Markierung für optionale Felder',
                    ],
                ],
            ],
            [
                'name' => 'buttons',
                'title' => 'Button Beschriftungen',
                'fields' => [
                    [
                        'name' => 'submit',
                        'type' => 'string',
                        'label' => 'Abschicken',
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
                        'label' => 'Standardtext',
                    ],
                ],
            ],
            [
                'name' => 'messages',
                'title' => 'Meldungen',
                'fields' => [
                    [
                        'name' => 'mail_sent_message',
                        'type' => 'string',
                        'label' => 'Email gesendet',
                    ],
                    [
                        'name' => 'mail_not_sent_message',
                        'type' => 'string',
                        'label' => 'Fehler beim senden',
                    ],
                ],
            ],
            [
                'name' => 'profile',
                'title' => 'Profilseiten',
                'fields' => [
                    [
                        'name' => 'show_email',
                        'type' => 'boolean',
                        'label' => 'Emailadresse anzeigen',
                    ],
                ],
            ],
        ],
        'links' => [
            'segment_pages' => [
                'startpage' => 'Startseite',
            ],
            'menus' => [
                'primary' => 'Kopfmenü',
                'footer' => 'Fußzeilenmenü',
                'shadow' => 'Nicht sichtbare Links',
            ],
            'files' => [
                'faviconSmall' => 'Kleines Favicon 64x64',
                'faviconShortcutIcon' => 'Kleines im ico Format',
                'faviconLarge' => 'Großes Favicon 512x512',
            ],
        ],
    ],
];
