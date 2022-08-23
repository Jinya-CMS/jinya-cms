export default {
  alert: {
    dismiss: 'Dismiss',
  },
  login: {
    menu: {
      title: 'Login',
      request_second_factor: 'Request second factor',
      login: 'login',
    },
    page: {
      label: {
        email: 'Email',
        password: 'Password',
        two_factor_code: 'Two factor code',
      },
      action: {
        request: 'Request second factor',
        login: 'Login',
      },
    },
    error: {
      login_failed: {
        title: 'Login failed',
        message: "Your email and password don't match, please use a different combination.",
      },
      '2fa_failed': {
        title: 'Login failed',
        message: 'Your two factor code is invalid, please check your emails for the correct code.',
      },
    },
  },
  media: {
    menu: {
      title: 'Media',
      files: 'Files',
      galleries: 'Galleries',
    },
    files: {
      action: {
        upload_single_file: 'Upload single file',
        upload_multiple_file: 'Upload multiple files',
        delete_file: 'Delete file',
        edit_file: 'Edit file',
      },
      delete: {
        title: 'Delete file',
        message: 'Do you really want to delete file {name}?',
        approve: 'Delete file',
        decline: 'Keep file',
        error: {
          title: 'Delete failed',
          conflict: 'The file {name} could not be deleted, because it is used.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      upload_single_file: {
        title: 'Upload file',
        file: 'File',
        name: 'Name',
        upload: 'Upload file',
        cancel: 'Cancel upload',
        error: {
          title: 'Upload failed',
          conflict: 'A file with the chosen name already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      upload_multiple_files: {
        title: 'Upload files',
        files: 'Files',
        upload: 'Upload files',
        cancel: 'Cancel upload',
        n_files_selected: '{length} file(s) selected',
      },
      edit: {
        title: 'Edit file',
        file: 'File',
        name: 'Name',
        save: 'Save file',
        cancel: 'Discard changes',
        error: {
          title: 'Edit failed',
          conflict: 'A file with the chosen name already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
    },
    galleries: {
      action: {
        new: 'Create gallery',
        edit: 'Edit gallery',
        delete: 'Delete gallery',
      },
      create: {
        title: 'Create gallery',
        name: 'Name',
        description: 'Description',
        orientation: 'Orientation',
        type: 'Type',
        horizontal: 'Horizontal',
        vertical: 'Vertical',
        masonry: 'Masonry',
        sequence: 'Sequence',
        create: 'Create gallery',
        cancel: 'Discard gallery',
        error: {
          title: 'Create failed',
          conflict: 'A gallery with the chosen name already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      edit: {
        title: 'Edit gallery',
        name: 'Name',
        description: 'Description',
        orientation: 'Orientation',
        type: 'Type',
        horizontal: 'Horizontal',
        vertical: 'Vertical',
        masonry: 'Masonry',
        sequence: 'Sequence',
        update: 'Update gallery',
        cancel: 'Discard changes',
        error: {
          title: 'Update failed',
          conflict: 'A gallery with the chosen name already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      delete: {
        title: 'Delete gallery',
        message: 'Do you really want to delete the gallery {name}?',
        keep: 'Keep gallery',
        delete: 'Delete gallery',
        error: {
          title: 'Delete failed',
          conflict: 'The gallery is used in a theme or menu and cannot be deleted.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      designer: {
        title: {
          vertical_sequence: 'vertical sequence gallery',
          horizontal_sequence: 'horizontal sequence gallery',
          vertical_masonry: 'vertical masonry gallery',
          horizontal_masonry: 'horizontal masonry gallery',
        },
      },
    },
  },
  pages_and_forms: {
    menu: {
      title: 'Pages & Forms',
      simple_pages: 'Simple pages',
      segment_pages: 'Segment pages',
      forms: 'Forms',
    },
    simple: {
      action: {
        new: 'Create page',
        edit: 'Change title',
        delete: 'Delete page',
        save_content: 'Save page',
        discard_content: 'Discard changes',
      },
      create: {
        title: 'Create page',
        page_title: 'Title',
        create: 'Create page',
        cancel: 'Discard page',
        error: {
          title: 'Create failed',
          conflict: 'A page with the chosen title already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      edit: {
        title: 'Edit page',
        page_title: 'Title',
        update: 'Update page',
        cancel: 'Discard changes',
        error: {
          title: 'Update failed',
          conflict: 'A page with the chosen title already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      delete: {
        title: 'Delete page',
        message: 'Do you really want to delete the page {title}?',
        keep: 'Keep page',
        delete: 'Delete page',
        error: {
          title: 'Delete failed',
          conflict: 'The page is used in a theme or menu and cannot be deleted.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
    },
    segment: {
      action: {
        new: 'Create page',
        edit: 'Change title',
        delete: 'Delete page',
        edit_segment: 'Edit segment',
        delete_segment: 'Delete segment',
      },
      create: {
        title: 'Create page',
        name: 'Name',
        create: 'Create page',
        cancel: 'Discard page',
        error: {
          title: 'Create failed',
          conflict: 'A page with the chosen title already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      edit: {
        title: 'Edit page',
        name: 'Name',
        update: 'Update page',
        cancel: 'Discard changes',
        error: {
          title: 'Update failed',
          conflict: 'A page with the chosen title already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      delete: {
        title: 'Delete page',
        message: 'Do you really want to delete the page {name}?',
        keep: 'Keep page',
        delete: 'Delete page',
        error: {
          title: 'Delete failed',
          conflict: 'The page is used in a theme or menu and cannot be deleted.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      delete_segment: {
        title: 'Delete segment',
        message: 'Do you really want to delete the selected segment?',
        keep: 'Keep segment',
        delete: 'Delete segment',
      },
      designer: {
        action: 'Action',
        link: 'Link',
        action_link: 'Link',
        action_none: 'None',
        file: 'File',
        gallery: 'Gallery',
        html: 'Formatted text',
        edit: {
          title: 'Edit segment',
          file: 'File',
          has_link: 'Has link',
          target: 'Target',
          gallery: 'Gallery',
          update: 'Update segment',
          cancel: 'Discard changes',
          error: {
            title: 'Update failed',
            generic: 'An unknown error occurred, please contact your administrator',
          },
        },
      },
    },
    form: {
      action: {
        new: 'Create form',
        edit: 'Edit form',
        delete: 'Delete form',
        edit_item: 'Edit item',
        delete_item: 'Delete item',
      },
      create: {
        title: 'Create form',
        form_title: 'Title',
        to_address: 'To address',
        description: 'Description',
        create: 'Create form',
        cancel: 'Discard form',
        error: {
          title: 'Create failed',
          conflict: 'A form with the chosen title already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      edit: {
        title: 'Edit form',
        form_title: 'Title',
        to_address: 'To address',
        description: 'Description',
        update: 'Update form',
        cancel: 'Discard changes',
        error: {
          title: 'Update failed',
          conflict: 'A form with the chosen title already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      delete: {
        title: 'Delete form',
        message: 'Do you really want to delete the form {title}?',
        keep: 'Keep form',
        delete: 'Delete form',
        error: {
          title: 'Delete failed',
          conflict: 'The form is used in a theme or menu and cannot be deleted.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      delete_item: {
        title: 'Delete item',
        message: 'Do you really want to delete the selected item?',
        keep: 'Keep item',
        delete: 'Delete item',
      },
      designer: {
        type_email: 'Email input',
        type_text: 'Text input',
        type_select: 'Dropdown',
        type_textarea: 'Multi line input',
        type_checkbox: 'Checkbox',
        edit: {
          title: 'Edit item',
          update: 'Update item',
          cancel: 'Discard changes',
          label: 'Label',
          placeholder: 'Placeholder',
          help_text: 'Help text',
          spam_filter: 'Spam filter',
          items: 'Items',
          is_subject: 'Is subject',
          is_from_address: 'Is from address',
          is_required: 'Is required',
        },
      },
    },
  },
  blog: {
    menu: {
      title: 'Blog',
      categories: 'Categories',
      posts: 'Posts',
    },
    categories: {
      action: {
        new: 'Create category',
        delete: 'Delete category',
        edit: 'Edit category',
      },
      details: {
        name: 'Name',
        description: 'Description',
        description_none: 'No description',
        parent: 'Parent category',
        parent_none: 'No parent category',
        webhook: 'Webhook',
      },
      create: {
        title: 'Create category',
        name: 'Name',
        description: 'Description',
        parent: 'Parent category',
        parent_none: 'No parent category',
        webhook_url: 'Webhook URL',
        webhook_enabled: 'Webhook enabled',
        cancel: 'Discard category',
        create: 'Create category',
        error: {
          title: 'Error creating category',
          conflict: 'A category with the chosen name already exists.',
          generic: 'An unknown error occured.',
        },
      },
      edit: {
        title: 'Edit category',
        name: 'Name',
        description: 'Description',
        parent: 'Parent category',
        parent_none: 'No parent category',
        webhook_url: 'Webhook URL',
        webhook_enabled: 'Webhook enabled',
        cancel: 'Discard changes',
        edit: 'Update category',
        error: {
          title: 'Error saving category',
          conflict: 'A category with the chosen name already exists.',
          generic: 'An unknown error occured.',
        },
      },
      delete: {
        title: 'Delete category',
        message: 'Do you really want to delete the category {name}?',
        keep: 'Keep category',
        delete: 'Delete category',
        error: {
          title: 'Delete failed',
          conflict: 'The category is used in a theme or menu and cannot be deleted.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
    },
    posts: {
      overview: {
        all: 'All Categories',
        action: {
          new: 'New post',
          delete: 'Delete post',
          edit: 'Edit post',
          designer: 'Postdesigner',
        },
        delete: {
          title: 'Delete post',
          message: 'Do you really want to delete the post {title}?',
          keep: 'Keep post',
          delete: 'Delete post',
        },
      },
      edit: {
        title: 'Edit post',
        category: 'Category',
        no_category: 'No Category',
        slug: 'Slug',
        post_title: 'Title',
        public: 'Public',
        no_header_image: 'No post image',
        header_image: 'Post image',
        update: 'Save post',
        cancel: 'Discard changes',
        error: {
          title: 'Error while saving',
          slug_exists: 'The chosen slug already exists',
          title_exists: 'The chosen title already exists',
          generic: 'An unknown error occurred saving the post',
        },
        success: {
          title: 'Post was saved',
          message: 'Your post was saved successfully',
        },
      },
      designer: {
        file: 'File',
        gallery: 'Gallery',
        html: 'Formatted text',
        link: 'Link',
        name: 'Name',
        action: {
          delete_segment: 'Delete segment',
          edit_segment: 'Edit segment',
        },
        edit: {
          title: 'Edit segment',
          file: 'File',
          has_link: 'Has link',
          link: 'Link',
          gallery: 'Gallery',
          update: 'Update segment',
          cancel: 'Discard changes',
          delete_segment: {
            title: 'Remove segment',
            message: 'Do you really want to remove the selected segment?',
            delete: 'Remove segment',
            keep: 'Keep segment',
          },
        },
        title: 'Post designer',
        cancel: 'Discard changes',
        save: 'Save changes',
      },
      create: {
        title: 'New post',
        category: 'Category',
        no_category: 'No Category',
        slug: 'Slug',
        post_title: 'Title',
        public: 'Public',
        no_header_image: 'No post image',
        header_image: 'Post image',
        create: 'Create post',
        cancel: 'Discard post',
        error: {
          title: 'Error while saving',
          slug_exists: 'The chosen slug already exists',
          title_exists: 'The chosen title already exists',
          generic: 'An unknown error occurred saving the post',
        },
        success: {
          title: 'Post was saved',
          message: 'Your post was saved successfully',
        },
      },
    },
  },
  design: {
    menu: {
      title: 'Design',
      themes: 'Theme',
      menus: 'Menus',
    },
    menus: {
      action: {
        new: 'Create menu',
        edit: 'Edit menu',
        delete: 'Delete menu',
        edit_item: 'Edit item',
        delete_item: 'Delete item',
        decrease_nesting: 'Decrease nesting',
        increase_nesting: 'Increase nesting',
      },
      create: {
        title: 'Create menu',
        name: 'Name',
        logo: 'Logo',
        logo_none: 'No logo',
        create: 'Create menu',
        cancel: 'Discard menu',
        error: {
          title: 'Create failed',
          conflict: 'A menu with the chosen name already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      edit: {
        title: 'Edit menu',
        name: 'Name',
        logo: 'Logo',
        logo_none: 'No logo',
        update: 'Update menu',
        cancel: 'Discard changes',
        error: {
          title: 'Update failed',
          conflict: 'A menu with the chosen name already exists.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      delete: {
        title: 'Delete menu',
        message: 'Do you really want to delete the menu {name}?',
        keep: 'Keep menu',
        delete: 'Delete menu',
        error: {
          title: 'Delete failed',
          conflict: 'The menu could not be deleted, because it is used in a theme.',
          generic: 'An unknown error occurred, please contact your administrator',
        },
      },
      delete_item: {
        title: 'Delete item',
        message: 'Do you really want to delete the selected item?',
        keep: 'Keep item',
        delete: 'Delete item',
      },
      designer: {
        type_gallery: 'Gallery',
        type_page: 'Simple page',
        type_segment_page: 'Segment page',
        type_form: 'Form',
        type_artist: 'Artist profile',
        type_group: 'Group',
        type_external_link: 'External link',
        type_blog_home_page: 'Blog home page',
        type_blog_category: 'Blog category',
        edit: {
          title: 'Edit item',
          update: 'Update item',
          cancel: 'Discard changes',
          item_title: 'Title',
          route: 'Link',
          is_highlighted: 'Is highlighted',
        },
      },
    },
    themes: {
      action: {
        activate: 'Activate theme',
        compile_assets: 'Generate assets',
        update: 'Update theme',
        create: 'Upload theme',
      },
      activate: {
        title: 'Activate theme',
        message: 'Do you want to activate the theme {displayName}?',
        approve: 'Activate theme',
        decline: "Don't activate theme",
        success: {
          title: 'Activate theme',
          message: 'The theme {displayName} was activated successfully',
        },
        error: {
          title: 'Error activating theme',
          message: 'The theme {displayName} could not be activated, please contact your administrator',
        },
      },
      assets: {
        title: 'Generate assets',
        message: 'Do you want to generate the assets for the theme {displayName}?',
        approve: 'Generate assets',
        decline: 'Cancel',
        success: {
          title: 'Generate assets',
          message: 'The assets for the theme {displayName} were generated successfully',
        },
        error: {
          title: 'Error generating assets',
          message: 'The assets for the theme {displayName} could not be generated, please contact your administrator',
        },
      },
      tabs: {
        details: 'Details',
        configuration: 'Configuration',
        links: 'Links',
        variables: 'Variables',
      },
      details: {
        preview: 'Theme preview',
      },
      variables: {
        save: 'Save variables',
        discard: 'Discard changes',
        error: {
          title: 'Error saving variables',
          message: 'The variables could not be saved, please contact your administrator.',
        },
        success: {
          title: 'Save variables',
          message: 'The variables were successfully saved',
        },
      },
      configuration: {
        save: 'Save configuration',
        discard: 'Discard changes',
        error: {
          title: 'Error saving configuration',
          message: 'The configuration could not be saved, please contact your administrator.',
        },
        success: {
          title: 'Save configuration',
          message: 'The configuration was successfully saved',
        },
      },
      links: {
        files: 'Files',
        galleries: 'Galleries',
        pages: 'Simple pages',
        segment_pages: 'Segment pages',
        forms: 'Forms',
        menus: 'Menus',
        categories: 'Categories',
        save: 'Save links',
        discard: 'Discard changes',
        error: {
          title: 'Error saving links',
          message: 'The links could not be saved, please contact your administrator.',
        },
        success: {
          title: 'Save links',
          message: 'The links  were successfully saved',
        },
      },
      update: {
        title: 'Update theme',
        file: 'ZIP archive',
        cancel: 'Cancel update',
        save: 'Update',
      },
      create: {
        title: 'Upload theme',
        name: 'Theme name',
        file: 'ZIP archive',
        cancel: 'Cancel upload',
        save: 'Upload',
      },
    },
  },
  my_jinya: {
    menu: {
      title: 'My Jinya',
      my_profile: 'My profile',
      active_sessions: 'Active sessions',
      active_devices: 'Active devices',
    },
    my_profile: {
      action: {
        change_password: 'Change password',
        edit_profile: 'Edit profile',
        save_profile: 'Save profile',
        discard_profile: 'Discard changes',
      },
      edit: {
        title: 'Update profile',
      },
      email: 'Email',
      artist_name: 'Artist name',
      profile_picture: 'Profile picture',
      about_me: 'About me',
      change_password: {
        title: 'Change password',
        new_password_repeat: 'Repeat password',
        new_password: 'New password',
        old_password: 'Old password',
        keep: 'Keep old password',
        change: 'Change password',
        error: {
          forbidden: 'The old password is wrong',
          generic: "Changing the password didn't work, please contact your administrator",
          title: 'Error changing password',
        },
        not_match: {
          title: "Passwords don't match",
          message: 'The new passwords need to match, please correct.',
        },
      },
    },
    sessions: {
      locating: 'Locating IP…',
      browser: '{browser} on {os}',
      device: '{vendor} {model}',
      unknown_device: 'Unknown device',
      action: {
        logout: 'Logout',
      },
      unknown: 'Unknown location',
      table: {
        ip: 'IP address',
        browser: 'Browser',
        device: 'Device',
        valid_since: 'Valid since',
        place: 'Place',
        actions: 'Actions',
      },
    },
    devices: {
      locating: 'Locating IP…',
      browser: '{browser} on {os}',
      device: '{vendor} {model}',
      unknown_device: 'Unknown device',
      action: {
        forget: 'Forget',
      },
      unknown: 'Unknown location',
      table: {
        ip: 'IP address',
        browser: 'Browser',
        device: 'Device',
        place: 'Place',
        actions: 'Actions',
      },
    },
  },
  maintenance: {
    menu: {
      title: 'Maintenance',
      update: 'Update',
      app_config: 'Jinya configuration',
      php_info: 'PHP info',
    },
    update: {
      version_text: 'You currently have version {openB}{currentVersion}{closeB} installed, there is an update available to {openB}{mostRecentVersion}{closeB}.',
      version_text_no_update: 'You currently have version {openB}{currentVersion}{closeB} installed, this is the most recent version.',
      update_now: 'Update now',
    },
    configuration: {
      key: 'Key',
      value: 'Value',
    },
    php: {
      configuration: 'Configuration',
      about: 'About the PHP installation',
      system_and_server: 'System and server',
      apache: 'Apache configuration',
      extension: {
        more_info: 'More informations',
        ini_values: 'Settings',
      },
      ini: {
        name: 'Name',
        value: 'Value',
      },
    },
  },
  database: {
    menu: {
      title: 'Database',
      mysql_info: 'MySQL info',
      tables: 'Tables',
      query_tool: 'Query tool',
    },
    mysql: {
      system_and_server: 'Server',
      global_variables: 'Global variables',
      local_variables: 'Local variables',
      session_variables: 'Session variables',
      server_type: 'Server type',
      server_version: 'Server version',
      compile_machine: 'Compile machine',
      compile_os: 'Compile operating system',
      name: 'Variable',
      value: 'Value',
    },
    tables: {
      field: 'Column name',
      type: 'Type',
      null: 'Allow null',
      key: 'Keys',
      default: 'Default value',
      extra: 'Extra data',
    },
    structure: 'Structure',
    constraints: {
      title: 'Constraints',
      constraintName: 'Name',
      constraintType: 'Constraint type',
      columnName: 'Column name',
      referencedTableName: 'Referenced table',
      referencedColumnName: 'Referenced column',
      positionInUniqueConstraint: 'Position in unique constraint',
      deleteRule: 'Action on delete',
      updateRule: 'Action on update',
      none: 'Not applicable',
    },
    indexes: {
      title: 'Indexes',
      keyName: 'Key name',
      columnName: 'Column name',
      cardinality: 'Cardinality',
      indexType: 'Index type',
      collation: 'Collation',
      unique: 'Unique',
    },
    details: 'Details',
    entryCount: 'Entry count',
    size: 'Table size in KB',
    engine: 'Storage engine',
    query_tool: {
      execute: 'Execute queries',
      no_result: 'No results were returned for the query',
      rows_affected: '{count} rows were affected by the query',
      error: {
        title: 'Error executing',
        message: 'There was an error executing the queries, please check the server logs',
      },
    },
  },
  artists: {
    menu: {
      title: 'Artists',
      artists: 'Artists',
    },
    action: {
      edit: 'Edit artist',
      delete: 'Delete artist',
      disable: 'Disable artist',
      enable: 'Enable artist',
      new: 'Create artist',
    },
    create: {
      title: 'Create artist',
      create: 'Create artist',
      cancel: 'Discard artist',
      name: 'Artist name',
      email: 'Email',
      password: 'Password',
      roles: 'Roles',
      is_reader: 'Reader',
      is_writer: 'Writer',
      is_admin: 'Admin',
      error: {
        title: 'Create failed',
        conflict: 'A artist with the chosen email already exists.',
        generic: 'An unknown error occurred, please contact your administrator',
      },
    },
    edit: {
      title: 'Edit artist',
      update: 'Update artist',
      cancel: 'Discard changes',
      name: 'Artist name',
      email: 'Email',
      password: 'Password',
      roles: 'Roles',
      is_reader: 'Reader',
      is_writer: 'Writer',
      is_admin: 'Admin',
      error: {
        title: 'Update failed',
        conflict: 'A artist with the chosen email already exists.',
        generic: 'An unknown error occurred, please contact your administrator',
      },
    },
    delete: {
      title: 'Delete artist',
      message: 'Do you really want to delete the artist {artistName}?',
      keep: 'Keep artist',
      delete: 'Delete artist',
    },
    disable: {
      title: 'Disable artist',
      message: 'Do you really want to disable the artist {artistName}?',
      keep: 'Keep artist enabled',
      delete: 'Disable artist',
    },
    enable: {
      title: 'Enable artist',
      message: 'Do you really want to enable the artist {artistName}?',
      keep: 'Keep artist disabled',
      delete: 'Enable artist',
    },
  },
  top_menu: {
    logout: 'Logout',
  },
  bottom_bar: {
    upload_title: {
      uploading: 'File upload pending…',
      uploaded: 'File upload done',
    },
    upload_progress: 'Uploaded {filesUploaded} of {filesToUpload} files',
  },
  statistics: {
    menu: {
      database: 'Database',
      matomo: 'Access',
      title: 'Statistics',
    },
    system: {
      title: 'Storage',
      free: 'Free storage',
      used: 'Used storage',
    },
    access: {
      browser: 'Visits by browser',
      country: 'Visits by country',
      device_brand: 'Visits by device brand',
      device_type: 'Visits by device type',
      language: 'Visits by language',
      operating_system: 'Visits by operating system',
      title: 'Access statistics for the last month',
      total_visits: 'visits',
    },
    database: {
      entities: 'Types',
      file: 'Files',
      galleries: 'Galleries',
      forms: 'Forms',
      simple_pages: 'Simple pages',
      segment_pages: 'Segment pages',
      blog_posts: 'Blog posts',
      blog_categories: 'Blog categories',
      history: {
        file: 'File history',
        created: 'Uploaded files',
        updated: 'Updated files',
      },
    },
  },
};
