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
  }
};