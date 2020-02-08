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
  },
};
