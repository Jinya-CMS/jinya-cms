export default {
    Account: {
        Login: {
            name: 'AccountLogin',
            route: '/designer/login',
        },
    },
    Error: {
        NotAllowed: {
            name: 'ErrorNotAllowed',
            route: '/designer/error/not-allowed',
        },
    },
    Home: {
        StartPage: {
            name: 'HomeStartPage',
            route: '/designer/',
        },
        Missing: {
            name: 'HomeMissing',
        },
    },
    Art: {
        Artworks: {
            SavedInJinya: {
                Overview: {
                    name: 'ArtArtworksSavedInJinyaOverview',
                    route: '/designer/art/artwork/jinya',
                },
                Details: {
                    name: 'ArtArtworksSavedInJinyaDetails',
                    route: '/designer/art/artwork/jinya/:slug',
                },
                Add: {
                    name: 'ArtArtworksSavedInJinyaAdd',
                    route: '/designer/art/artwork/jinya/add',
                },
                Edit: {
                    name: 'ArtArtworksSavedInJinyaEdit',
                    route: '/designer/art/artwork/jinya/:slug/edit',
                },
            },
            SavedExternal: {
                Overview: {
                    name: 'ArtArtworksSavedExternalOverview',
                    route: '/designer/art/artwork/external',
                },
                Details: {
                    name: 'ArtArtworksSavedExternalDetails',
                    route: '/designer/art/artwork/external/:slug',
                },
                Add: {
                    name: 'ArtArtworksSavedExternalAdd',
                    route: '/designer/art/artwork/external/add',
                },
                Edit: {
                    name: 'ArtArtworksSavedExternalEdit',
                    route: '/designer/art/artwork/external/:slug/edit',
                },
            },
        },
        Videos: {
            SavedInJinya: {
                Overview: {
                    name: 'ArtVideosSavedInJinyaOverview',
                    route: '/designer/art/video/jinya',
                },
                Add: {
                    name: 'ArtVideosSavedInJinyaAdd',
                    route: '/designer/art/video/jinya/add',
                },
                Edit: {
                    name: 'ArtVideosSavedInJinyaEdit',
                    route: '/designer/art/video/jinya/:slug/edit',
                },
                Details: {
                    name: 'ArtVideosSavedInJinyaDetails',
                    route: '/designer/art/video/jinya/:slug',
                },
                Uploader: {
                    name: 'ArtVideosSavedInJinyaUploader',
                    route: '/designer/art/video/jinya/:slug/upload',
                },
            },
            SavedOnYoutube: {
                Overview: {
                    name: 'ArtVideosSavedOnYoutubeOverview',
                    route: '/designer/art/video/youtube',
                },
                Add: {
                    name: 'ArtVideosSavedOnYoutubeAdd',
                    route: '/designer/art/video/youtube/add',
                },
                Edit: {
                    name: 'ArtVideosSavedOnYoutubeEdit',
                    route: '/designer/art/video/youtube/:slug/edit',
                },
                Details: {
                    name: 'ArtVideosSavedOnYoutubeDetails',
                    route: '/designer/art/video/youtube/:slug',
                },
            },
            SavedOnVimeo: {
                Overview: {
                    name: 'ArtVideosSavedOnVimeoOverview',
                    route: '/designer/art/video/vimeo',
                },
                Add: {
                    name: 'ArtVideosSavedOnVimeoAdd',
                    route: '/designer/art/video/vimeo/add',
                },
                Edit: {
                    name: 'ArtVideosSavedOnVimeoEdit',
                    route: '/designer/art/video/vimeo/:slug/edit',
                },
                Details: {
                    name: 'ArtVideosSavedOnVimeoDetails',
                    route: '/designer/art/video/vimeo/:slug',
                },
            },
            SavedOnNewgrounds: {
                Overview: {
                    name: 'ArtVideosSavedOnNewgroundsOverview',
                    route: '/designer/art/video/newgrounds',
                },
                Add: {
                    name: 'ArtVideosSavedOnNewgroundsAdd',
                    route: '/designer/art/video/newgrounds/add',
                },
                Edit: {
                    name: 'ArtVideosSavedOnNewgroundsEdit',
                    route: '/designer/art/video/newgrounds/:slug/edit',
                },
                Details: {
                    name: 'ArtVideosSavedOnNewgroundsDetails',
                    route: '/designer/art/video/newgrounds/:slug',
                },
            },
        },
        Galleries: {
            Art: {
                Overview: {
                    name: 'ArtGalleriesArtOverview',
                    route: '/designer/art/gallery/art',
                },
                Details: {
                    name: 'ArtGalleriesArtDetails',
                    route: '/designer/art/gallery/art/:slug',
                },
                Designer: {
                    name: 'ArtGalleriesArtDesigner',
                    route: '/designer/art/gallery/art/:slug/designer',
                },
                Add: {
                    name: 'ArtGalleriesArtAdd',
                    route: '/designer/art/gallery/art/add',
                },
                Edit: {
                    name: 'ArtGalleriesArtEdit',
                    route: '/designer/art/gallery/art/:slug/edit',
                },
            },
            Video: {
                Overview: {
                    name: 'ArtGalleriesVideoOverview',
                    route: '/designer/art/gallery/video',
                },
                Details: {
                    name: 'ArtGalleriesVideoDetails',
                    route: '/designer/art/gallery/video/:slug',
                },
                Designer: {
                    name: 'ArtGalleriesVideoDesigner',
                    route: '/designer/art/gallery/video/:slug/designer',
                },
                Add: {
                    name: 'ArtGalleriesVideoAdd',
                    route: '/designer/art/gallery/video/add',
                },
                Edit: {
                    name: 'ArtGalleriesVideoEdit',
                    route: '/designer/art/gallery/video/:slug/edit',
                },
            },
        },
    },
    Media: {
        Files: {
            FileBrowser: {
                name: 'MediaFilesBrowser',
                route: '/designer/media/browser',
            },
        },
        Galleries: {
            Overview: {
                name: 'MediaGalleriesOverview',
                route: '/designer/media/gallery',
            },
            Arrange: {
                name: 'MediaGalleriesArrange',
                route: '/designer/media/gallery/:id/arrange',
            },
        },
    },
    Static: {
        Pages: {
            SavedInJinya: {
                Overview: {
                    name: 'StaticPagesSavedInJinyaOverview',
                    route: '/designer/static/page/jinya',
                },
                Details: {
                    name: 'StaticPagesSavedInJinyaDetails',
                    route: '/designer/static/page/jinya/:slug',
                },
                Add: {
                    name: 'StaticPagesSavedInJinyaAdd',
                    route: '/designer/static/page/jinya/add',
                },
                Edit: {
                    name: 'StaticPagesSavedInJinyaEdit',
                    route: '/designer/static/page/jinya/:slug/edit',
                },
            },
            Segment: {
                Overview: {
                    name: 'StaticPagesSegmentOverview',
                    route: '/designer/static/page/segment',
                },
                Details: {
                    name: 'StaticPagesSegmentDetails',
                    route: '/designer/static/page/segment/:slug',
                },
                Add: {
                    name: 'StaticPagesSegmentAdd',
                    route: '/designer/static/page/segment/add',
                },
                Edit: {
                    name: 'StaticPagesSegmentEdit',
                    route: '/designer/static/page/segment/:slug/edit',
                },
                Editor: {
                    name: 'StaticPagesSegmentEditor',
                    route: '/designer/static/page/segment/:slug/editor',
                },
            },
            SavedExternal: {
                Overview: {
                    name: 'StaticPagesSavedExternalOverview',
                    route: '/designer/static/page/external',
                },
                Details: {
                    name: 'StaticPagesSavedExternalDetails',
                    route: '/designer/static/page/external/:slug',
                },
                Add: {
                    name: 'StaticPagesSavedExternalAdd',
                    route: '/designer/static/page/external/add',
                },
                Edit: {
                    name: 'StaticPagesSavedExternalEdit',
                    route: '/designer/static/page/external/:slug/edit',
                },
            },
        },
        Forms: {
            Forms: {
                Overview: {
                    name: 'StaticFormsFormsOverview',
                    route: '/designer/static/forms/forms',
                },
                Add: {
                    name: 'StaticFormsFormsAdd',
                    route: '/designer/static/forms/forms/add',
                },
                Edit: {
                    name: 'StaticFormsFormsEdit',
                    route: '/designer/static/forms/forms/:slug/edit',
                },
                Items: {
                    name: 'StaticFormsFormsItems',
                    route: '/designer/static/forms/forms/:slug/items',
                },
            },
            EmailTemplates: {
                Overview: {
                    name: 'StaticFormsEmailTemplatesOverview',
                    route: '/designer/static/forms/emailtemplates',
                },
                Add: {
                    name: 'StaticFormsEmailTemplatesAdd',
                    route: '/designer/static/forms/emailtemplates/add',
                },
                Edit: {
                    name: 'StaticFormsEmailTemplatesEdit',
                    route: '/designer/static/forms/emailtemplates/:slug/edit',
                },
                Details: {
                    name: 'StaticFormsEmailTemplatesDetails',
                    route: '/designer/static/forms/emailtemplates/:slug',
                },
            },
            Messages: {
                Overview: {
                    name: 'StaticFormsMessagesOverview',
                    route: '/designer/static/forms/messages',
                },
                Action: {
                    name: 'StaticFormsMessagesAction',
                    route: '/designer/static/forms/messages/action/:action',
                },
                Form: {
                    name: 'StaticFormsMessagesForm',
                    route: '/designer/static/forms/messages/form/:slug',
                },
            },
        },
    },
    Configuration: {
        General: {
            Artists: {
                Overview: {
                    name: 'ConfigurationGeneralArtistsOverview',
                    route: '/designer/configuration/artists',
                },
                Details: {
                    name: 'ConfigurationGeneralArtistsDetails',
                    route: '/designer/configuration/artists/:id',
                },
                Add: {
                    name: 'ConfigurationGeneralArtistsAdd',
                    route: '/designer/configuration/artists/add',
                },
                Edit: {
                    name: 'ConfigurationGeneralArtistsEdit',
                    route: '/designer/configuration/artists/:id/edit',
                },
            },
        },
        Frontend: {
            Menu: {
                Overview: {
                    name: 'ConfigurationFrontendMenusOverview',
                    route: '/designer/configuration/frontend/menu',
                },
                Add: {
                    name: 'ConfigurationFrontendMenusAdd',
                    route: '/designer/configuration/frontend/menu/add',
                },
                Edit: {
                    name: 'ConfigurationFrontendMenusEdit',
                    route: '/designer/configuration/frontend/menu/:id/edit',
                },
                Builder: {
                    name: 'ConfigurationFrontendMenusBuilder',
                    route: '/designer/configuration/frontend/menu/:id/builder',
                },
            },
            Theme: {
                Overview: {
                    name: 'ConfigurationFrontendThemeOverview',
                    route: '/designer/configuration/frontend/theme',
                },
                Settings: {
                    name: 'ConfigurationFrontendThemeSettings',
                    route: '/designer/configuration/frontend/theme/:name/settings',
                },
                Links: {
                    name: 'ConfigurationFrontendThemeLinks',
                    route: '/designer/configuration/frontend/theme/:name/links',
                },
                Variables: {
                    name: 'ConfigurationFrontendThemeVariables',
                    route: '/designer/configuration/frontend/theme/:name/variables',
                },
            },
        },
    },
    Maintenance: {
        System: {
            Updates: {
                name: 'MaintenanceSystemUpdates',
                route: '/designer/maintenance/updates',
            },
            Environment: {
                name: 'MaintenanceSystemEnvironment',
                route: '/designer/maintenance/environment',
            },
            Cache: {
                name: 'MaintenanceSystemCache',
                route: '/designer/maintenance/cache',
            },
            Version: {
                name: 'MaintenanceSystemVersion',
                route: '/designer/maintenance/version',
            },
            PHP: {
                name: 'MaintenanceSystemPHP',
                route: '/designer/maintenance/PHP',
            },
        },
        Database: {
            MySQL: {
                name: 'MaintenanceDatabaseMySQL',
                route: '/designer/maintenance/database/mysql',
            },
            Tool: {
                name: 'MaintenanceDatabaseTool',
                route: '/designer/maintenance/database/tool',
            },
        },
        Diagnosis: {
            ApplicationLog: {
                Overview: {
                    name: 'MaintenanceDiagnosisApplicationLogOverview',
                    route: '/designer/maintenance/applicationlog',
                },
                Details: {
                    name: 'MaintenanceDiagnosisApplicationLogDetails',
                    route: '/designer/maintenance/applicationlog/:id',
                },
                Clear: {
                    name: 'MaintenanceDiagnosisApplicationLogEdit',
                    route: '/designer/maintenance/applicationlog/clear',
                },
            },
            AccessLog: {
                Overview: {
                    name: 'MaintenanceDiagnosisAccessLogOverview',
                    route: '/designer/maintenance/accesslog',
                },
                Details: {
                    name: 'MaintenanceDiagnosisAccessLogDetails',
                    route: '/designer/maintenance/accesslog/:id',
                },
                Clear: {
                    name: 'MaintenanceDiagnosisAccessLogEdit',
                    route: '/designer/maintenance/accesslog/clear',
                },
            },
        },
    },
    MyJinya: {
        Account: {
            Profile: {
                name: 'MyJinyaAccountProfile',
                route: '/designer/myjinya',
            },
            Edit: {
                name: 'MyJinyaAccountEdit',
                route: '/designer/myjinya/edit',
            },
            Password: {
                name: 'MyJinyaAccountPassword',
                route: '/designer/myjinya/password',
            },
            ApiKeys: {
                name: 'MyJinyaAccountApiKeys',
                route: '/designer/myjinya/api-keys',
            },
        },
        TwoFactor: {
            KnownDevices: {
                name: 'MyJinyaAccountKnownDevices',
                route: '/designer/myjinya/known-devices',
            },
        },
        CreatedByMe: {
            Artworks: {
                name: 'MyJinyaCreatedByMeArtworks',
                route: '/designer/createdbyme/artworks',
            },
            Videos: {
                name: 'MyJinyaCreatedByMeVideos',
                route: '/designer/createdbyme/videos',
            },
            Galleries: {
                name: 'MyJinyaCreatedByMeGalleries',
                route: '/designer/createdbyme/galleries',
            },
            Pages: {
                name: 'MyJinyaCreatedByMePages',
                route: '/designer/createdbyme/pages',
            },
            Forms: {
                name: 'MyJinyaCreatedByMeForms',
                route: '/designer/createdbyme/forms',
            },
            Links: {
                name: 'MyJinyaCreatedByMeMenus',
                route: '/designer/createdbyme/menus',
            },
        },
    },
    };
