export default {
  Account: {
    Login: {
      name: 'AccountLogin',
      route: '/designer/login'
    }
  },
  Home: {
    StartPage: {
      name: 'HomeStartPage',
      route: '/designer'
    },
    Missing: {
      name: 'HomeMissing'
    }
  },
  Art: {
    Artworks: {
      SavedInJinya: {
        Overview: {
          name: 'ArtArtworksSavedInJinyaOverview',
          route: '/designer/art/artwork/jinya'
        },
        Details: {
          name: 'ArtArtworksSavedInJinyaDetails',
          route: '/designer/art/artwork/jinya/:slug'
        },
        Add: {
          name: 'ArtArtworksSavedInJinyaAdd',
          route: '/designer/art/artwork/jinya/add'
        },
        Edit: {
          name: 'ArtArtworksSavedInJinyaEdit',
          route: '/designer/art/artwork/jinya/:slug/edit'
        },
        Delete: {
          name: 'ArtArtworksSavedInJinyaDelete',
          route: '/designer/art/artwork/jinya/:slug/delete'
        }
      },
      SavedExternal: {
        Overview: {
          name: 'ArtArtworksSavedExternalOverview',
          route: '/designer/art/artwork/external'
        },
        Details: {
          name: 'ArtArtworksSavedExternalDetails',
          route: '/designer/art/artwork/external/:slug'
        },
        Add: {
          name: 'ArtArtworksSavedExternalAdd',
          route: '/designer/art/artwork/external/add'
        },
        Edit: {
          name: 'ArtArtworksSavedExternalEdit',
          route: '/designer/art/artwork/external/:slug/edit'
        },
        Delete: {
          name: 'ArtArtworksSavedExternalDelete',
          route: '/designer/art/artwork/external/:slug/delete'
        }
      }
    },
    Videos: {
      SavedInJinya: {
        Overview: {
          name: 'ArtVideosSavedInJinyaOverview',
          route: '/designer/art/video/jinya'
        },
        Add: {
          name: 'ArtVideosSavedInJinyaAdd',
          route: '/designer/art/video/jinya/add'
        },
        Edit: {
          name: 'ArtVideosSavedInJinyaEdit',
          route: '/designer/art/video/jinya/:slug/edit'
        },
        Delete: {
          name: 'ArtVideosSavedInJinyaDelete',
          route: '/designer/art/video/jinya/:slug/delete'
        },
        Details: {
          name: 'ArtVideosSavedInJinyaDetails',
          route: '/designer/art/video/jinya/:slug'
        }
      },
      SavedOnYoutube: {
        Overview: {
          name: 'ArtVideosSavedOnYoutubeOverview',
          route: '/designer/art/video/youtube'
        },
        Add: {
          name: 'ArtVideosSavedOnYoutubeAdd',
          route: '/designer/art/video/youtube/add'
        },
        Edit: {
          name: 'ArtVideosSavedOnYoutubeEdit',
          route: '/designer/art/video/youtube/:slug/edit'
        },
        Delete: {
          name: 'ArtVideosSavedOnYoutubeDelete',
          route: '/designer/art/video/youtube/:slug/delete'
        },
        Details: {
          name: 'ArtVideosSavedOnYoutubeDetails',
          route: '/designer/art/video/youtube/:slug'
        }
      },
      SavedOnVimeo: {
        Overview: {
          name: 'ArtVideosSavedOnVimeoOverview',
          route: '/designer/art/video/vimeo'
        },
        Add: {
          name: 'ArtVideosSavedOnVimeoAdd',
          route: '/designer/art/video/vimeo/add'
        },
        Edit: {
          name: 'ArtVideosSavedOnVimeoEdit',
          route: '/designer/art/video/vimeo/:slug/edit'
        },
        Delete: {
          name: 'ArtVideosSavedOnVimeoDelete',
          route: '/designer/art/video/vimeo/:slug/delete'
        },
        Details: {
          name: 'ArtVideosSavedOnVimeoDetails',
          route: '/designer/art/video/vimeo/:slug'
        }
      },
      SavedOnNewgrounds: {
        Overview: {
          name: 'ArtVideosSavedOnNewgroundsOverview',
          route: '/designer/art/video/newgrounds'
        },
        Add: {
          name: 'ArtVideosSavedOnNewgroundsAdd',
          route: '/designer/art/video/newgrounds/add'
        },
        Edit: {
          name: 'ArtVideosSavedOnNewgroundsEdit',
          route: '/designer/art/video/newgrounds/:slug/edit'
        },
        Delete: {
          name: 'ArtVideosSavedOnNewgroundsDelete',
          route: '/designer/art/video/newgrounds/:slug/delete'
        },
        Details: {
          name: 'ArtVideosSavedOnNewgroundsDetails',
          route: '/designer/art/video/newgrounds/:slug'
        }
      }
    },
    Galleries: {
      Art: {
        Overview: {
          name: 'ArtGalleriesArtOverview',
          route: '/designer/art/gallery/art'
        },
        Details: {
          name: 'ArtGalleriesArtDetails',
          route: '/designer/art/gallery/art/:slug'
        },
        Add: {
          name: 'ArtGalleriesArtAdd',
          route: '/designer/art/gallery/art/add'
        },
        Edit: {
          name: 'ArtGalleriesArtEdit',
          route: '/designer/art/gallery/art/:slug/edit'
        },
        Delete: {
          name: 'ArtGalleriesArtDelete',
          route: '/designer/art/gallery/art/:slug/delete'
        }
      },
      Video: {
        Overview: {
          name: 'ArtGalleriesVideoOverview',
          route: '/designer/art/gallery/video'
        },
        Details: {
          name: 'ArtGalleriesVideoDetails',
          route: '/designer/art/gallery/video/:slug'
        },
        Add: {
          name: 'ArtGalleriesVideoAdd',
          route: '/designer/art/gallery/video/add'
        },
        Edit: {
          name: 'ArtGalleriesVideoEdit',
          route: '/designer/art/gallery/video/:slug/edit'
        },
        Delete: {
          name: 'ArtGalleriesVideoDelete',
          route: '/designer/art/gallery/video/:slug/delete'
        }
      }
    }
  },
  Static: {
    Pages: {
      SavedInJinya: {
        Overview: {
          name: 'StaticPagesSavedInJinyaOverview',
          route: '/designer/static/page/jinya'
        },
        Details: {
          name: 'StaticPagesSavedInJinyaDetails',
          route: '/designer/static/page/jinya/:slug'
        },
        Add: {
          name: 'StaticPagesSavedInJinyaAdd',
          route: '/designer/static/page/jinya/add'
        },
        Edit: {
          name: 'StaticPagesSavedInJinyaEdit',
          route: '/designer/static/page/jinya/:slug/edit'
        },
        Delete: {
          name: 'StaticPagesSavedInJinyaDelete',
          route: '/designer/static/page/jinya/:slug/delete'
        }
      },
      SavedExternal: {
        Overview: {
          name: 'StaticPagesSavedExternalOverview',
          route: '/designer/static/page/external'
        },
        Details: {
          name: 'StaticPagesSavedExternalDetails',
          route: '/designer/static/page/external/:slug'
        },
        Add: {
          name: 'StaticPagesSavedExternalAdd',
          route: '/designer/static/page/external/add'
        },
        Edit: {
          name: 'StaticPagesSavedExternalEdit',
          route: '/designer/static/page/external/:slug/edit'
        },
        Delete: {
          name: 'StaticPagesSavedExternalDelete',
          route: '/designer/static/page/external/:slug/delete'
        }
      }
    },
    Forms: {
      Forms: {
        Overview: {
          name: 'StaticFormsFormsOverview',
          route: '/designer/static/forms/forms'
        },
        Add: {
          name: 'StaticFormsFormsAdd',
          route: '/designer/static/forms/forms/add'
        },
        Edit: {
          name: 'StaticFormsFormsEdit',
          route: '/designer/static/forms/forms/:slug/edit'
        },
        Delete: {
          name: 'StaticFormsFormsDelete',
          route: '/designer/static/forms/forms/:slug/delete'
        },
        Details: {
          name: 'StaticFormsFormsDetails',
          route: '/designer/static/forms/forms/:slug'
        }
      },
      EmailTemplates: {
        Overview: {
          name: 'StaticFormsEmailTemplatesOverview',
          route: '/designer/static/forms/emailtemplates'
        },
        Add: {
          name: 'StaticFormsEmailTemplatesAdd',
          route: '/designer/static/forms/emailtemplates/add'
        },
        Edit: {
          name: 'StaticFormsEmailTemplatesEdit',
          route: '/designer/static/forms/emailtemplates/:slug/edit'
        },
        Delete: {
          name: 'StaticFormsEmailTemplatesDelete',
          route: '/designer/static/forms/emailtemplates/:slug/delete'
        },
        Details: {
          name: 'StaticFormsEmailTemplatesDetails',
          route: '/designer/static/forms/emailtemplates/:slug'
        }
      },
      Requests: {
        Overview: {
          name: 'StaticFormsRequestsOverview',
          route: '/designer/static/forms/requests'
        },
        Reply: {
          name: 'StaticFormsRequestsReply',
          route: '/designer/static/forms/requests/:slug/reply'
        },
        Close: {
          name: 'StaticFormsRequestsClose',
          route: '/designer/static/forms/requests/:slug/close'
        },
        Details: {
          name: 'StaticFormsRequestsDetails',
          route: '/designer/static/forms/requests/:slug'
        }
      }
    }
  },
  Configuration: {
    General: {
      Artists: {
        Overview: {
          name: 'ConfigurationGeneralArtistsOverview',
          route: '/designer/configuration/artists'
        },
        Details: {
          name: 'ConfigurationGeneralArtistsDetails',
          route: '/designer/configuration/artists/:slug'
        },
        Add: {
          name: 'ConfigurationGeneralArtistsAdd',
          route: '/designer/configuration/artists/add'
        },
        Edit: {
          name: 'ConfigurationGeneralArtistsEdit',
          route: '/designer/configuration/artists/:slug/edit'
        },
        Delete: {
          name: 'ConfigurationGeneralArtistsDelete',
          route: '/designer/configuration/artists/:slug/delete'
        }
      }
    },
    Frontend: {
      Menu: {
        Overview: {
          name: 'ConfigurationFrontendMenusOverview',
          route: '/designer/configuration/frontend/menu'
        },
        Add: {
          name: 'ConfigurationFrontendMenusAdd',
          route: '/designer/configuration/frontend/menu/add'
        },
        Edit: {
          name: 'ConfigurationFrontendMenusEdit',
          route: '/designer/configuration/frontend/menu/:slug/edit'
        },
        Delete: {
          name: 'ConfigurationFrontendMenusDelete',
          route: '/designer/configuration/frontend/menu/:slug/delete'
        },
        Details: {
          name: 'ConfigurationFrontendMenusDetails',
          route: '/designer/configuration/frontend/menu/:slug'
        }
      },
      Theme: {
        Overview: {
          name: 'ConfigurationFrontendThemeOverview',
          route: '/designer/configuration/frontend/theme'
        },
        Edit: {
          name: 'ConfigurationFrontendThemeEdit',
          route: '/designer/configuration/frontend/theme/:slug'
        }
      }
    }
  },
  Maintenance: {
    System: {
      Updates: {
        name: 'MaintenanceSystemUpdates',
        route: '/designer/maintenance/updates'
      },
      Environment: {
        name: 'MaintenanceSystemEnvironment',
        route: '/designer/maintenance/environment'
      },
      Cache: {
        name: 'MaintenanceSystemCache',
        route: '/designer/maintenance/cache'
      },
      Version: {
        name: 'MaintenanceSystemVersion',
        route: '/designer/maintenance/version'
      },
      PHP: {
        name: 'MaintenanceSystemPHP',
        route: '/designer/maintenance/PHP'
      }
    },
    Database: {
      MySQL: {
        name: 'MaintenanceDatabaseMySQL',
        route: '/designer/maintenance/database/mysql'
      },
      Tool: {
        name: 'MaintenanceDatabaseTool',
        route: '/designer/maintenance/database/tool'
      }
    },
    Diagnosis: {
      ApplicationLog: {
        Overview: {
          name: 'MaintenanceDiagnosisApplicationLogOverview',
          route: '/designer/maintenance/applicationlog'
        },
        Details: {
          name: 'MaintenanceDiagnosisApplicationLogDetails',
          route: '/designer/maintenance/applicationlog/:id'
        },
        Clear: {
          name: 'MaintenanceDiagnosisApplicationLogEdit',
          route: '/designer/maintenance/applicationlog/clear'
        }
      },
      AccessLog: {
        Overview: {
          name: 'MaintenanceDiagnosisAccessLogOverview',
          route: '/designer/maintenance/accesslog'
        },
        Details: {
          name: 'MaintenanceDiagnosisAccessLogDetails',
          route: '/designer/maintenance/accesslog/:id'
        },
        Clear: {
          name: 'MaintenanceDiagnosisAccessLogEdit',
          route: '/designer/maintenance/accesslog/clear'
        }
      }
    }
  },
  MyJinya: {
    Account: {
      Profile: {
        name: 'MyJinyaAccountProfile',
        route: '/designer/myjinya'
      },
      Edit: {
        name: 'MyJinyaAccountEdit',
        route: '/designer/myjinya/edit'
      },
      Password: {
        name: 'MyJinyaAccountPassword',
        route: '/designer/myjinya/password'
      }
    },
    CreatedByMe: {
      Artworks: {
        name: 'MyJinyaCreatedByMeArtworks',
        route: '/designer/createdbyme/artworks'
      },
      Videos: {
        name: 'MyJinyaCreatedByMeVideos',
        route: '/designer/createdbyme/videos'
      },
      Galleries: {
        name: 'MyJinyaCreatedByMeGalleries',
        route: '/designer/createdbyme/galleries'
      },
      Pages: {
        name: 'MyJinyaCreatedByMePages',
        route: '/designer/createdbyme/pages'
      },
      Forms: {
        name: 'MyJinyaCreatedByMeForms',
        route: '/designer/createdbyme/forms'
      },
      Menus: {
        name: 'MyJinyaCreatedByMeMenus',
        route: '/designer/createdbyme/menus'
      }
    }
  }
};