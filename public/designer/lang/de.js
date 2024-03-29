export default {
  alert: {
    dismiss: 'Ausblenden',
  },
  file_picker: {
    dismiss: 'Dialog schließen',
    pick: 'Datei auswählen',
    title: 'Datei auswählen',
  },
  login: {
    menu: {
      title: 'Anmelden',
      request_second_factor: 'Zweiten Faktor anfordern',
      login: 'Anmelden',
    },
    page: {
      label: {
        email: 'Email',
        password: 'Passwort',
        two_factor_code: 'Zwei-Faktor-Code',
      },
      action: {
        request: 'Zweiten Faktor anfordern',
        login: 'Anmelden',
      },
    },
    error: {
      login_failed: {
        title: 'Anmeldung fehlgeschlagen',
        message: 'Die Emailadresse und das Passwort können nicht gefunden werden.',
      },
      '2fa_failed': {
        title: 'Anmeldung fehlgeschlagen',
        message: 'Der Zwei-Faktor-Code ist falsch, bitte prüf deine Emails nach dem richtigen Code.',
      },
    },
  },
  media: {
    menu: {
      title: 'Medien',
      files: 'Dateien',
      galleries: 'Galerien',
    },
    files: {
      action: {
        upload_single_file: 'Eine Datei hochladen',
        upload_multiple_file: 'Mehrere Dateien hochladen',
        delete_file: 'Datei löschen',
        edit_file: 'Datei bearbeiten',
        manage_tags: 'Tags verwalten',
        show_all_tags: 'Alle Tags',
      },
      delete: {
        title: 'Datei löschen',
        message: 'Willst du die Datei {name} wirklich löschen?',
        approve: 'Datei löschen',
        decline: 'Datei behalten',
        error: {
          title: 'Löschen fehlgeschlagen',
          conflict: 'Die Datei {name} konnte nicht gelöscht werden, da sie benutzt wird.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      upload_single_file: {
        title: 'Datei hochladen',
        file: 'Datei',
        name: 'Name',
        tags: 'Tags',
        upload: 'Datei hochladen',
        cancel: 'Hochladen abbrechen',
        error: {
          title: 'Hochladen fehlgeschlagen',
          conflict: 'Eine Datei mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      upload_multiple_files: {
        title: 'Dateien hochladen',
        files: 'Dateien',
        tags: 'Tags',
        upload: 'Dateien hochladen',
        cancel: 'Hochladen abbrechen',
        n_files_selected: '{length} Datei(en) ausgewählt',
      },
      edit: {
        title: 'Datei bearbeiten',
        file: 'Datei',
        name: 'Name',
        tags: 'Tags',
        save: 'Datei speichern',
        cancel: 'Änderungen verwerfen',
        error: {
          title: 'Speichern fehlgeschlagen',
          conflict: 'Eine Datei mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      tags: {
        manage: {
          title: 'Tags verwalten',
          close: 'Fertig',
        },
        popup: {
          name: 'Name',
          emoji: 'Symbol',
          color: 'Color',
        },
        new: {
          title: 'New tag',
          save: 'Create tag',
          cancel: 'Discard',
          error: {
            title: 'Error',
            exists: 'A tag with the given name already exists',
            generic: 'An unknown error occurred, please try again later',
            close: 'Close',
          },
        },
        edit: {
          title: 'Edit tag',
          save: 'Save tag',
          cancel: 'Discard changes',
          error: {
            title: 'Error',
            exists: 'A tag with the given name already exists',
            generic: 'An unknown error occurred, please try again later',
            close: 'Close',
          },
        },
        delete: {
          title: 'Delete tag',
          message: 'Do you really want to delete the tag {name}?',
          approve: 'Delete tag',
          decline: 'Keep tag',
          error: {
            title: 'Error',
            generic: 'An unknown error occurred, please try again later',
            close: 'Close',
          },
        },
      },
      details: {
        type: 'Typ',
        uploadedBy: 'Hochgeladen von',
        lastChangedBy: 'Zuletzt geändert von',
        downloadFile: 'Datei herunterladen',
        types: {
          font: 'Schriftart',
          'image/jpeg': 'JPEG Bild',
          'image/gif': 'GIF Bild',
          'image/png': 'PNG Bild',
          'image/webp': 'WEBP Bild',
          'image/svg+xml': 'SVG Bild',
          'image/avif': 'AVIF Bild',
          'image/vnd.microsoft.icon': 'Icon',
          'video/x-msvideo': 'AVI Video',
          'video/mp4': 'MP4 Video',
          'video/mpeg': 'MPEG Video',
          'video/ogg': 'OGG Video',
          'video/webm': 'WEBM Video',
          'audio/aac': 'AAC Audio',
          'audio/mpeg': 'MP3 Audio',
          'audio/ogg': 'OGG Audio',
          'audio/opus': 'Opus Audio',
          'audio/wav': 'WAV Audio',
          'audio/webm': 'WEBM Audio',
        },
      },
    },
    galleries: {
      action: {
        new: 'Galerie erstellen',
        edit: 'Galerie bearbeiten',
        delete: 'Galerie löschen',
        show_all_tags: 'Alle Tags',
      },
      create: {
        title: 'Galerie erstellen',
        name: 'Name',
        description: 'Beschreibung',
        orientation: 'Orientierung',
        type: 'Typ',
        horizontal: 'Horizontal',
        vertical: 'Vertikal',
        masonry: 'Raster',
        sequence: 'Liste',
        create: 'Galerie erstellen',
        cancel: 'Galerie verwerfen',
        error: {
          title: 'Erstellen fehlgeschlagen',
          conflict: 'Eine Galerie mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      edit: {
        title: 'Galerie bearbeiten',
        name: 'Name',
        description: 'Beschreibung',
        orientation: 'Orientierung',
        type: 'Typ',
        horizontal: 'Horizontal',
        vertical: 'Vertikal',
        masonry: 'Raster',
        sequence: 'Liste',
        update: 'Galerie speichern',
        cancel: 'Änderungen verwerfen',
        error: {
          title: 'Speichern fehlgeschlagen',
          conflict: 'Eine Galerie mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      delete: {
        title: 'Galerie löschen',
        message: 'Soll die Galerie {name} wirklich gelöscht werden?',
        keep: 'Galerie behalten',
        delete: 'Galerie löschen',
        error: {
          title: 'Löschen fehlgeschlagen',
          conflict: 'Die Galerie konnte nicht gelöscht werden, da sie benutzt wird.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      designer: {
        title: {
          vertical_sequence: 'vertikale Listengalerie',
          horizontal_sequence: 'horizontale Listengalerie',
          vertical_masonry: 'vertikale Rastergalerie',
          horizontal_masonry: 'horizontale Rastergalerie',
        },
      },
    },
  },
  pages_and_forms: {
    menu: {
      title: 'Seiten & Formulare',
      simple_pages: 'Einfache Seiten',
      segment_pages: 'Segmentseiten',
      forms: 'Formulare',
    },
    simple: {
      action: {
        new: 'Seite erstellen',
        edit: 'Titel ändern',
        delete: 'Seite löschen',
        save_content: 'Seite speichern',
        discard_content: 'Änderungen verwerfen',
      },
      create: {
        title: 'Seite erstellen',
        page_title: 'Titel',
        create: 'Seite erstellen',
        cancel: 'Seite verwerfen',
        error: {
          title: 'Erstellen fehlgeschlagen',
          conflict: 'Eine Seite mit dem gewählten Titel existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      edit: {
        title: 'Seite bearbeiten',
        page_title: 'Titel',
        update: 'Seite speichern',
        cancel: 'Änderungen verwerfen',
        error: {
          title: 'Speichern fehlgeschlagen',
          conflict: 'Eine Seite mit dem gewählten Titel existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      delete: {
        title: 'Seite löschen',
        message: 'Soll die Seite {title} wirklich gelöscht werden?',
        keep: 'Seite behalten',
        delete: 'Seite löschen',
        error: {
          title: 'Löschen fehlgeschlagen',
          conflict: 'Die Seite konnte nicht gelöscht werden, da sie benutzt wird.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
    },
    segment: {
      action: {
        new: 'Seite erstellen',
        edit: 'Titel ändern',
        delete: 'Seite löschen',
        edit_segment: 'Segment bearbeiten',
        delete_segment: 'Segment löschen',
      },
      create: {
        title: 'Seite erstellen',
        name: 'Name',
        create: 'Seite erstellen',
        cancel: 'Seite verwerfen',
        error: {
          title: 'Erstellen fehlgeschlagen',
          conflict: 'Eine Seite mit dem gewählten Titel existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      edit: {
        title: 'Seite bearbeiten',
        name: 'Name',
        update: 'Seite speichern',
        cancel: 'Änderungen verwerfen',
        error: {
          title: 'Speichern fehlgeschlagen',
          conflict: 'Eine Seite mit dem gewählten Titel existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      delete: {
        title: 'Seite löschen',
        message: 'Soll die Seite {name} wirklich gelöscht werden?',
        keep: 'Seite behalten',
        delete: 'Seite löschen',
        error: {
          title: 'Löschen fehlgeschlagen',
          conflict: 'Die Seite konnte nicht gelöscht werden, da sie benutzt wird.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      delete_segment: {
        title: 'Segment löschen',
        message: 'Soll das ausgewählte Segment wirklich gelöscht werden?',
        keep: 'Segment behalten',
        delete: 'Segment löschen',
      },
      designer: {
        action: 'Aktion',
        link: 'Link',
        action_link: 'Link',
        action_none: 'Keine',
        file: 'Datei',
        gallery: 'Galerie',
        html: 'Formatierter text',
        edit: {
          title: 'Segment bearbeiten',
          file: 'Datei',
          please_select: 'Bitte eine Datei wählen',
          has_link: 'Hat Link',
          target: 'Ziel-URL',
          gallery: 'Galerie',
          update: 'Segment speichern',
          cancel: 'Änderungen verwerfen',
          error: {
            title: 'Speichern fehlgeschalgen',
            generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
          },
        },
      },
    },
    form: {
      action: {
        new: 'Formular erstellen',
        edit: 'Formular bearbeiten',
        delete: 'Formular löschen',
        edit_item: 'Element bearbeiten',
        delete_item: 'Element löschen',
      },
      create: {
        title: 'Formular erstellen',
        form_title: 'Titel',
        to_address: 'Empfängeradresse',
        description: 'Beschreibung',
        create: 'Formular erstellen',
        cancel: 'Formular verwerfen',
        error: {
          title: 'Erstellen fehlgeschlagen',
          conflict: 'Ein Formular mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      edit: {
        title: 'Formular bearbeiten',
        form_title: 'Titel',
        to_address: 'Empfängeradresse',
        description: 'Beschreibung',
        update: 'Formular speichern',
        cancel: 'Änderungen verwerfen',
        error: {
          title: 'Speichern fehlgeschlagen',
          conflict: 'Ein Formular mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      delete: {
        title: 'Formular löschen',
        message: 'Soll das Formular {title} wirklich gelöscht werden?',
        keep: 'Formular behalten',
        delete: 'Formular löschen',
        error: {
          title: 'Löschen fehlgeschlagen',
          conflict: 'Das Formular konnte nicht gelöscht werden, da es benutzt wird.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      delete_item: {
        title: 'Element löschen',
        message: 'Soll das ausgewählte Element wirklich gelöscht werden?',
        keep: 'Element behalten',
        delete: 'Element löschen',
      },
      designer: {
        type_email: 'Emailfeld',
        type_text: 'Textfeld',
        type_select: 'Dropdown',
        type_textarea: 'Mehrzeiliger Text',
        type_checkbox: 'Checkbox',
        edit: {
          title: 'Element bearbeiten',
          update: 'Element speichern',
          cancel: 'Änderungen verwerfen',
          label: 'Label',
          placeholder: 'Platzhaltertext',
          help_text: 'Hilfetext',
          spam_filter: 'Spamfilter',
          items: 'Elemente',
          is_subject: 'Betreff',
          is_from_address: 'Absenderadresse',
          is_required: 'Erforderlich',
        },
      },
    },
  },
  blog: {
    menu: {
      title: 'Blog',
      categories: 'Kategorien',
      posts: 'Artikel',
    },
    categories: {
      action: {
        new: 'Kategorie erstellen',
        delete: 'Kategorie löschen',
        edit: 'Kategorie bearbeiten',
      },
      details: {
        name: 'Name',
        description: 'Beschreibung',
        description_none: 'Keine Beschreibung',
        parent: 'Übergeordnete Kategorie',
        parent_none: 'Keine Kategorie',
        webhook: 'Webhook',
      },
      create: {
        title: 'Kategorie erstellen',
        name: 'Name',
        description: 'Beschreibung',
        parent: 'Übergeordnete Kategorie',
        parent_none: 'Keine Kategorie',
        webhook_url: 'Webhook URL',
        webhook_enabled: 'Webhook aktiv',
        cancel: 'Kategorie verwerfen',
        create: 'Kategorie erstellen',
        error: {
          title: 'Fehler beim Erstellen',
          conflict: 'Eine Kategorie mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten.',
        },
      },
      edit: {
        title: 'Kategorie bearbeiten',
        name: 'Name',
        description: 'Beschreibung',
        parent: 'Übergeordnete Kategorie',
        parent_none: 'Keine Kategorie',
        webhook_url: 'Webhook URL',
        webhook_enabled: 'Webhook aktiv',
        cancel: 'Änderungen verwerfen',
        edit: 'Kategorie speichern',
        error: {
          title: 'Fehler beim Speichern',
          conflict: 'Eine Kategorie mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten.',
        },
      },
      delete: {
        title: 'Kategorie löschen',
        message: 'Soll die Kategorie {name} wirklich gelöscht werden?',
        keep: 'Kategorie behalten',
        delete: 'Kategorie löschen',
        error: {
          title: 'Löschen fehlgeschlagen',
          conflict: 'Die Kategorie konnte nicht gelöscht werden, da sie benutzt wird.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
    },
    posts: {
      overview: {
        all: 'Alle Kategorien',
        action: {
          new: 'Neuer Artikel',
          delete: 'Artikel löschen',
          edit: 'Artikel bearbeiten',
          designer: 'Artikeldesigner',
        },
        delete: {
          title: 'Artikel löschen',
          message: 'Soll der Artikel {title} wirklich gelöscht werden?',
          keep: 'Artikel behalten',
          delete: 'Artikel löschen',
        },
      },
      edit: {
        title: 'Artikel bearbeiten',
        category: 'Kategorie',
        no_category: 'Keine Kategorie',
        slug: 'Slug',
        post_title: 'Titel',
        public: 'Öffentlich',
        no_header_image: 'Kein Artikelbild',
        header_image: 'Artikelbild',
        update: 'Artikel speichern',
        cancel: 'Änderungen verwerfen',
        error: {
          title: 'Fehler beim Speichern',
          slug_exists: 'Der gewählte Slug ist bereits vergeben',
          title_exists: 'Der gewählte Titel ist bereits vergeben',
          generic: 'Ein unbekannter Fehler ist aufgetreten',
        },
        success: {
          title: 'Artikel gespeichert',
          message: 'Der Artikel wurde erfolgreich gespeichert',
        },
      },
      create: {
        title: 'Neuer Artikel',
        category: 'Kategorie',
        no_category: 'Keine Kategorie',
        slug: 'Slug',
        post_title: 'Titel',
        public: 'Öffentlich',
        no_header_image: 'Kein Artikelbild',
        header_image: 'Artikelbild',
        create: 'Artikel erstellen',
        cancel: 'Artikel verwerfen',
        error: {
          title: 'Fehler beim Speichern',
          slug_exists: 'Der gewählte Slug ist bereits vergeben',
          title_exists: 'Der gewählte Titel ist bereits vergeben',
          generic: 'Ein unbekannter Fehler ist aufgetreten',
        },
        success: {
          title: 'Artikel gespeichert',
          message: 'Der Artikel wurde erfolgreich gespeichert',
        },
      },
      designer: {
        file: 'Datei',
        gallery: 'Galerie',
        html: 'Formatierter Text',
        link: 'Link',
        name: 'Name',
        action: {
          delete_segment: 'Segment löschen',
          edit_segment: 'Segment bearbeiten',
        },
        edit: {
          title: 'Segment bearbeiten',
          file: 'Datei',
          has_link: 'Mit link',
          link: 'Link',
          gallery: 'Galerie',
          please_select: 'Bitte eine Datei wählen',
          update: 'Segment speichern',
          cancel: 'Änderungen verwerfen',
          delete_segment: {
            title: 'Segment löschen',
            message: 'Soll das Segment wirklich gelöscht werden?',
            delete: 'Segment löschen',
            keep: 'Segment behalten',
          },
        },
        title: 'Artikeldesigner',
        cancel: 'Änderungen verwerfen',
        save: 'Artikel speichern',
      },
    },
  },
  design: {
    menu: {
      title: 'Design',
      themes: 'Themes',
      menus: 'Menüs',
    },
    menus: {
      action: {
        new: 'Menü erstellen',
        edit: 'Menü bearbeiten',
        delete: 'Menü löschen',
        edit_item: 'Element bearbeiten',
        delete_item: 'Element löschen',
        decrease_nesting: 'Einzug verringern',
        increase_nesting: 'Einzug erhöhen',
      },
      create: {
        title: 'Menü erstellen',
        name: 'Name',
        logo: 'Logo',
        logo_none: 'Kein Logo',
        create: 'Menü erstellen',
        cancel: 'Menü löschen',
        error: {
          title: 'Erstellen fehlgeschlagen',
          conflict: 'Ein Menü mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      edit: {
        title: 'Menü bearbeiten',
        name: 'Name',
        logo: 'Logo',
        logo_none: 'Kein Logo',
        update: 'Menü speichern',
        cancel: 'Änderungen verwerfen',
        error: {
          title: 'Speichern fehlgeschlagen',
          conflict: 'Ein Menü mit dem gewählten Namen existiert bereits.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      delete: {
        title: 'Menü löschen',
        message: 'Soll das Menü {name} wirklich gelöscht werden?',
        keep: 'Menü behalten',
        delete: 'Menü löschen',
        error: {
          title: 'Löschen fehlgeschlagen',
          conflict: 'Das Menü konnte nicht gelöscht werden, da es verwendet wird.',
          generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
        },
      },
      delete_item: {
        title: 'Element löschen',
        message: 'Soll das ausgewählte Element wirklich gelöscht werden?',
        keep: 'Element behalten',
        delete: 'Element löschen',
      },
      designer: {
        type_gallery: 'Galerie',
        type_page: 'Einfache Seite',
        type_segment_page: 'Segmentseite',
        type_form: 'Formular',
        type_artist: 'Künstlerprofil',
        type_group: 'Gruppe',
        type_external_link: 'Externer Link',
        type_blog_home_page: 'Blog Startseite',
        type_blog_category: 'Blog Kategorie',
        edit: {
          title: 'Element bearbeiten',
          update: 'Element speichern',
          cancel: 'Änderungen verwerfen',
          item_title: 'Titel',
          route: 'Link',
          is_highlighted: 'Hervorgehoben',
        },
      },
    },
    themes: {
      action: {
        activate: 'Theme aktivieren',
        compile_assets: 'Themedateien erzeugen und kopieren',
        update: 'Theme aktualisieren',
        create: 'Theme hochladen',
      },
      activate: {
        title: 'Theme aktivieren',
        message: 'Soll das Theme {displayName} wirklich aktiviert werden?',
        approve: 'Theme aktivieren',
        decline: 'Theme nicht aktivieren',
        success: {
          title: 'Theme aktivieren',
          message: 'Das Theme {displayName} wurde erfolgreich aktiviert',
        },
        error: {
          title: 'Theme aktivieren fehlgeschlagen',
          message: 'Das Theme {displayName} konnte nicht aktiviert werden, bitte kontaktiere deinen Administrator',
        },
      },
      assets: {
        title: 'Themedateien erzeugen und kopieren',
        message: 'Willst du die Themedateien für das Theme {displayName} erzeugen und kopieren?',
        approve: 'Themedateien erzeugen und kopieren',
        decline: 'Cancel',
        success: {
          title: 'Themedateien erzeugen und kopieren',
          message: 'Die Themedateien {displayName} wurde erfolgreich erzeugt und kopiert',
        },
        error: {
          title: 'Themedateien erzeugen und kopieren fehlgeschlagen',
          message:
            'Die Themedateien {displayName} konnten nicht erzeugt und kopiert werden, bitte kontaktiere deinen Administrator',
        },
      },
      tabs: {
        details: 'Details',
        configuration: 'Konfiguration',
        links: 'Links',
        variables: 'Variablen',
      },
      details: {
        preview: 'Themevorschau',
      },
      variables: {
        save: 'Variablen speichern',
        discard: 'Änderungen verwerfen',
        error: {
          title: 'Variablen speichern fehlgeschlagen',
          message: 'Die Variablen konnten nicht gespeichert werden, bitte kontaktiere deinen Administrator.',
        },
        success: {
          title: 'Variablen speichern',
          message: 'Die Variablen wurden erfolgreich gespeichert',
        },
      },
      configuration: {
        save: 'Konfiguration speichern',
        discard: 'Änderungen verwerfen',
        error: {
          title: 'Konfiguration speichern fehlgeschlagen',
          message: 'Die Konfiguration konnte nicht gespeichert werden, bitte kontaktiere deinen Administrator.',
        },
        success: {
          title: 'Konfiguration speichern',
          message: 'Die Konfiguration wurde erfolgreich gespeichert',
        },
      },
      links: {
        files: 'Dateien',
        galleries: 'Galerien',
        pages: 'Einfache Seiten',
        segment_pages: 'Segmentseiten',
        forms: 'Formulare',
        menus: 'Menüs',
        categories: 'Kategorien',
        save: 'Links speichern',
        discard: 'Änderungen verwerfen',
        error: {
          title: 'Links speichern fehlgeschlagen',
          message: 'Die Links konnten nicht gespeichert werden, bitte kontaktiere deinen Administrator.',
        },
        success: {
          title: 'Links speichern',
          message: 'Die Links wurden erfolgreich gespeichert',
        },
      },
      create: {
        name: 'Themename',
        file: 'ZIP Archiv',
        title: 'Theme hochladen',
        cancel: 'Hochladen abbrechen',
        save: 'Hochladen',
      },
      update: {
        file: 'ZIP Archiv',
        title: 'Theme updaten',
        cancel: 'Update abbrechen',
        save: 'Update starten',
      },
    },
  },
  my_jinya: {
    menu: {
      title: 'Mein Jinya',
      my_profile: 'Mein Profil',
      active_sessions: 'Aktive Anmeldungen',
      active_devices: 'Gemerkte Geräte',
    },
    my_profile: {
      action: {
        change_password: 'Passwort ändern',
        edit_profile: 'Profil bearbeiten',
        save_profile: 'Profil speichern',
        discard_profile: 'Änderungen verwerfen',
      },
      edit: {
        title: 'Profil bearbeiten',
      },
      email: 'Email',
      artist_name: 'Künstlername',
      about_me: 'Über mich',
      profile_picture: 'Profilbild',
      change_password: {
        title: 'Passwort ändern',
        new_password_repeat: 'Passwort wiederholen',
        new_password: 'Neues Passwort',
        old_password: 'Altes passwort',
        keep: 'Alter Passwort behalten',
        change: 'Passwort ändern',
        error: {
          forbidden: 'Das alte Passwort ist falsch',
          generic: 'Das Passwort konnte nicht geändert werden, bitte wende dich an deinen Administrator',
          title: 'Passwort ändern fehlgeschlagen',
        },
        not_match: {
          title: 'Passwörter unterscheiden sich',
          message: 'Die neuen Passwörte müssen die gleichen sein.',
        },
      },
    },
    sessions: {
      locating: 'IP wird geortet…',
      browser: '{browser} auf {os}',
      device: '{vendor} {model}',
      unknown_device: 'Unbekanntes Gerät',
      action: {
        logout: 'Abmelden',
      },
      unknown: 'Unbekannter Ort',
      table: {
        ip: 'IP Adresse',
        browser: 'Browser',
        device: 'Gerät',
        valid_since: 'Gültig seit',
        place: 'Ort',
        actions: 'Aktionen',
      },
    },
    devices: {
      locating: 'IP wird geortet…',
      browser: '{browser} auf {os}',
      device: '{vendor} {model}',
      unknown_device: 'Unbekanntes Gerät',
      action: {
        forget: 'Vergessen',
      },
      unknown: 'Unbekannter Ort',
      table: {
        ip: 'IP Adresse',
        browser: 'Browser',
        device: 'Gerät',
        place: 'Ort',
        actions: 'Aktionen',
      },
    },
  },
  maintenance: {
    menu: {
      title: 'Wartung',
      update: 'Update',
      app_config: 'Jinya Konfiguration',
      php_info: 'PHP Info',
    },
    update: {
      version_text:
        'Aktuell ist Version {openB}{currentVersion}{closeB} installiert. Es gibt ein Update auf Version {openB}{mostRecentVersion}{closeB}.',
      version_text_no_update:
        'Aktuell ist Version {openB}{currentVersion}{closeB} installiert, dies ist die aktuellste Version.',
      update_now: 'Jetzt updaten',
      perform_update: {
        title: 'Update durchführen',
        message: 'Soll das Update für Jinya CMS jetzt installiert werden? Ein Abbruch ist nicht möglich.',
        decline: 'Update abbrechen',
        approve: 'Jetzt updaten',
        updating: 'Jinya CMS wird aktualisiert',
        failed: {
          title: 'Fehler',
          message: 'Das Update ist fehlgeschlagen, bitte versuch es später erneut.',
        },
      },
    },
    configuration: {
      key: 'Schlüssel',
      value: 'Wert',
    },
    php: {
      configuration: 'Konfiguration',
      about: 'Über die PHP Installation',
      system_and_server: 'System und Server',
      apache: 'Apache Konfiguration',
      extension: {
        more_info: 'Mehr Informationen',
        ini_values: 'Einstellungen',
      },
      ini: {
        name: 'Name',
        value: 'Wert',
      },
    },
  },
  database: {
    menu: {
      title: 'Datenbank',
      mysql_info: 'MySQL info',
      tables: 'Tabellen',
      query_tool: 'Query tool',
    },
    mysql: {
      system_and_server: 'Server',
      global_variables: 'Globale Variablen',
      local_variables: 'Lokale Variablen',
      session_variables: 'Session Variablen',
      server_type: 'Servertyp',
      server_version: 'Server Version',
      compile_machine: 'Compile machine',
      compile_os: 'Compile operating system',
      name: 'Variable',
      value: 'Wert',
    },
    tables: {
      field: 'Spaltenname',
      type: 'Typ',
      null: 'Erlaubt null',
      key: 'Schlüssel',
      default: 'Standardwert',
      extra: 'Extradaten',
    },
    constraints: {
      title: 'Constraints',
      constraintName: 'Name',
      constraintType: 'Constrainttyp',
      columnName: 'Spalten Name',
      referencedTableName: 'Referenzierte Tabelle',
      referencedColumnName: 'Referenzierte Spalte',
      positionInUniqueConstraint: 'Position in Unique Constraint',
      deleteRule: 'Aktion bei Löschen',
      updateRule: 'Aktion bei Update',
      none: 'Nicht anwendbar',
    },
    indexes: {
      title: 'Indexes',
      keyName: 'Schlüsselname',
      columnName: 'Spalten Name',
      cardinality: 'Kardinalität',
      indexType: 'Indextyp',
      collation: 'Collation',
      unique: 'Einzigartig',
    },
    structure: 'Struktur',
    details: 'Details',
    entryCount: 'Anzahl Einträge',
    size: 'Tabellengröße in KB',
    engine: 'Speicher Engine',
    query_tool: {
      execute: 'Queries ausführen',
      no_result: 'Für diese Query gibt es keine Ergebnisse',
      rows_affected: '{count} Zeilen waren von der Query betroffen',
      error: {
        title: 'Queries ausführen fehlgeschlagen',
        message: 'Es gab einen Fehler beim Ausführen der Queries',
      },
    },
  },
  artists: {
    menu: {
      title: 'Künstler',
      artists: 'Künstler',
    },
    action: {
      edit: 'Künstler bearbeiten',
      delete: 'Künstler löschen',
      disable: 'Künstler deaktivieren',
      enable: 'Künstler aktivieren',
      new: 'Künstler erstellen',
    },
    create: {
      title: 'Künstler erstellen',
      create: 'Künstler erstellen',
      cancel: 'Künstler verwerfen',
      name: 'Künstlername',
      email: 'Email',
      password: 'Passwort',
      roles: 'Rollen',
      is_reader: 'Reader',
      is_writer: 'Writer',
      is_admin: 'Admin',
      error: {
        title: 'Erstellen fehlgeschlagen',
        conflict: 'Ein Künstler mit der gewählten Emailadresse existiert bereits.',
        generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
      },
    },
    edit: {
      title: 'Künstler bearbeiten',
      update: 'Künstler speichern',
      cancel: 'Änderungen verwerfen',
      name: 'Künstlername',
      email: 'Email',
      password: 'Passwort',
      roles: 'Rollen',
      is_reader: 'Reader',
      is_writer: 'Writer',
      is_admin: 'Admin',
      error: {
        title: 'Speichern fehlgeschlagen',
        conflict: 'Ein Künstler mit der gewählten Emailadresse existiert bereits.',
        generic: 'Ein unbekannter Fehler ist aufgetreten, bitte kontaktiere deinen Administrator',
      },
    },
    delete: {
      title: 'Künstler löschen',
      message: 'Soll der Künstler {artistName} wirklich gelöscht werden?',
      keep: 'Keep artist',
      delete: 'Künstler löschen',
    },
    disable: {
      title: 'Künstler deaktivieren',
      message: 'Soll der Künstler {artistName} wirklich deaktiviert werden?',
      keep: 'Künstler aktiviert lassen',
      delete: 'Künstler deaktivieren',
    },
    enable: {
      title: 'Künstler aktivieren',
      message: 'Soll der Künstler {artistName} wirklich aktiviert werden?',
      keep: 'Künstler deaktiviert lassen',
      delete: 'Künstler aktivieren',
    },
  },
  top_menu: {
    logout: 'Abmelden',
  },
  bottom_bar: {
    upload_title: {
      uploading: 'Dateien werden hochgeladen…',
      uploaded: 'Alle Dateien wurden hochgeladen',
    },
    upload_progress: '{filesUploaded} vom {filesToUpload} Dateien hochgeladen',
  },
  statistics: {
    menu: {
      database: 'Datenbank',
      matomo: 'Zugriffe',
      title: 'Statistiken',
    },
    system: {
      title: 'Speicher',
      free: 'Freier Speicher',
      used: 'Belegter Speicher',
    },
    access: {
      browser: 'Zugriffe nach Browser',
      country: 'Zugriffe nach Land',
      device_brand: 'Zugriffe nach Gerätemarke',
      device_type: 'Zugriffe nach Gerätetyp',
      language: 'Zugriffe nach Sprache',
      operating_system: 'Zugriffe nach Betriebssystem',
      title: 'Zugriffstatistiken des letzten Monats',
      total_visits: 'Zugriffe',
    },
    database: {
      entities: 'Typen',
      file: 'Dateien',
      galleries: 'Galerien',
      forms: 'Formulare',
      simple_pages: 'Einfache Seiten',
      segment_pages: 'Segmentseiten',
      blog_posts: 'Blogartikel',
      blog_categories: 'Blogkategorien',
      history: {
        file: 'Dateihistorie',
        created: 'Hochgeladene Dateien',
        updated: 'Geänderte Dateien',
      },
    },
  },
};
