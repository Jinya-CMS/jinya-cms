import { Alpine } from '../../../../lib/alpine.js';
import { getClassicPages } from '../../foundation/api/classic-pages.js';
import {
  activateTheme,
  compileThemeAssets,
  getTheme,
  getThemeBlogCategories,
  getThemeClassicPages,
  getThemeConfigurationStructure,
  getThemeDefaultConfiguration,
  getThemeFiles,
  getThemeForms,
  getThemeGalleries,
  getThemeMenus,
  getThemeModernPages,
  getThemes,
  getThemeStyleVariables,
  updateTheme,
  updateThemeBlogCategory,
  updateThemeClassicPage,
  updateThemeConfiguration,
  updateThemeFile,
  updateThemeForm,
  updateThemeGallery,
  updateThemeMenu,
  updateThemeModernPage,
  updateThemeVariables,
  uploadTheme,
} from '../../foundation/api/themes.js';
import { getForms } from '../../foundation/api/forms.js';
import { getGalleries } from '../../foundation/api/galleries.js';
import { getFiles } from '../../foundation/api/files.js';
import { getModernPages } from '../../foundation/api/modern-pages.js';
import { getMenus } from '../../foundation/api/menus.js';
import { getBlogCategories } from '../../foundation/api/blog-categories.js';
import localize from '../../foundation/utils/localize.js';
import confirm from '../../foundation/ui/confirm.js';

Alpine.data('themesData', () => ({
  themes: [],
  linksLoaded: false,
  selectedTheme: null,
  themeSelected: false,
  activeTab: 'details',
  tiny: null,
  getValueForCurrentLanguage(data) {
    const currentLanguage = navigator.language.substring(0, 2);
    if (Object.prototype.hasOwnProperty.call(data, currentLanguage)) {
      return data[currentLanguage];
    }
    if (Object.prototype.hasOwnProperty.call(data, 'en')) {
      return data.en;
    }

    return data;
  },
  getVariableLabel(variable) {
    return variable
      .replace('$', '')
      .replaceAll('-', ' ')
      .replace(/(^\w)|(\s+\w)/g, (letter) => letter.toUpperCase());
  },
  get title() {
    return this.selectedThemeDisplayName;
  },
  get selectedThemeDisplayName() {
    return this.getValueForCurrentLanguage(this.selectedTheme.displayName);
  },
  get selectedThemePreviewImage() {
    return `/api/theme/${this.selectedTheme.id}/preview`;
  },
  get selectedThemeDescription() {
    return this.getValueForCurrentLanguage(this.selectedTheme.description);
  },
  async init() {
    const themes = await getThemes();
    this.themes = themes.items;
    if (this.themes.length > 0) {
      await this.selectTheme(this.themes[0]);
    }
  },
  selectedThemeData: {
    modernPages: {},
    classicPages: {},
    forms: {},
    menus: {},
    galleries: {},
    files: {},
    blogCategories: {},
    styleVariables: {},
    scssVariables: {},
    defaultConfiguration: {},
    configurationStructure: {},
    configuration: {},
  },
  openCreateDialog() {
    this.upload.error.reset();
    this.upload.file = null;
    this.upload.open = true;
  },
  openEditDialog() {
    this.edit.error.reset();
    this.edit.file = null;
    this.edit.open = true;
  },
  loadThemeConfiguration() {
    const configuration = Array.isArray(this.selectedTheme.configuration) ? {} : this.selectedTheme.configuration;

    for (const group of this.selectedThemeData.configurationStructure.groups) {
      configuration[group.name] = configuration[group.name] ?? {};
      for (const field of group.fields) {
        configuration[group.name][field.name] = configuration[group.name][field.name] ?? null;
      }
    }

    this.selectedThemeData.configuration = configuration;
  },
  loadThemeVariables() {
    this.selectedThemeData.scssVariables = Array.isArray(this.selectedTheme.scssVariables)
      ? {}
      : this.selectedTheme.scssVariables;
  },
  async loadLinks() {
    const [modernPages, classicPages, forms, menus, galleries, files, blogCategories] = await Promise.all([
      getThemeModernPages(this.selectedTheme.id),
      getThemeClassicPages(this.selectedTheme.id),
      getThemeForms(this.selectedTheme.id),
      getThemeMenus(this.selectedTheme.id),
      getThemeGalleries(this.selectedTheme.id),
      getThemeFiles(this.selectedTheme.id),
      getThemeBlogCategories(this.selectedTheme.id),
    ]);

    this.selectedThemeData.modernPages = {};
    for (const [key, item] of Object.entries(modernPages)) {
      this.selectedThemeData.modernPages[key] = item.id;
    }

    this.selectedThemeData.classicPages = {};
    for (const [key, item] of Object.entries(classicPages)) {
      this.selectedThemeData.classicPages[key] = item.id;
    }

    this.selectedThemeData.forms = {};
    for (const [key, item] of Object.entries(forms)) {
      this.selectedThemeData.forms[key] = item.id;
    }

    this.selectedThemeData.menus = {};
    for (const [key, item] of Object.entries(menus)) {
      this.selectedThemeData.menus[key] = item.id;
    }

    this.selectedThemeData.galleries = {};
    for (const [key, item] of Object.entries(galleries)) {
      this.selectedThemeData.galleries[key] = item.id;
    }

    this.selectedThemeData.files = {};
    for (const [key, item] of Object.entries(files)) {
      this.selectedThemeData.files[key] = item.id;
    }

    this.selectedThemeData.blogCategories = {};
    for (const [key, item] of Object.entries(blogCategories)) {
      this.selectedThemeData.blogCategories[key] = item.id;
    }
  },
  async loadConfigurationStructure() {
    const [styleVariables, defaultConfiguration, configurationStructure] = await Promise.all([
      getThemeStyleVariables(this.selectedTheme.id),
      getThemeDefaultConfiguration(this.selectedTheme.id),
      getThemeConfigurationStructure(this.selectedTheme.id),
    ]);

    this.selectedThemeData.styleVariables = styleVariables;
    this.selectedThemeData.defaultConfiguration = defaultConfiguration;
    this.selectedThemeData.configurationStructure = configurationStructure;
  },
  async selectTheme(theme) {
    this.activeTab = 'details';

    if (!this.linksLoaded) {
      const [galleries, files, classicPages, modernPages, menus, blogCategories, forms] = await Promise.all([
        getGalleries(),
        getFiles(),
        getClassicPages(),
        getModernPages(),
        getMenus(),
        getBlogCategories(),
        getForms(),
      ]);

      this.galleries = galleries.items;
      this.files = files.items;
      this.classicPages = classicPages.items;
      this.modernPages = modernPages.items;
      this.menus = menus.items;
      this.blogCategories = blogCategories.items;
      this.forms = forms.items;
      this.linksLoaded = true;
    }

    this.selectedTheme = await getTheme(theme.id);
    await Promise.all([this.loadConfigurationStructure(), this.loadLinks()]);

    this.loadThemeConfiguration();
    this.loadThemeVariables();

    this.themeSelected = true;
  },
  async saveConfiguration() {
    try {
      await updateThemeConfiguration(this.selectedTheme.id, this.selectedThemeData.configuration);
      this.configuration.message.title = localize({ key: 'design.themes.configuration.success.title' });
      this.configuration.message.content = localize({ key: 'design.themes.configuration.success.message' });
      this.configuration.message.hasError = false;
      this.configuration.message.hasMessage = true;
      setTimeout(() => {
        this.configuration.message.hasMessage = false;
      }, 30000);
    } catch (e) {
      this.configuration.message.title = localize({ key: 'design.themes.configuration.error.title' });
      this.configuration.message.content = localize({ key: 'design.themes.configuration.error.message' });
      this.configuration.message.hasError = true;
      this.configuration.message.hasMessage = true;
    }
  },
  async saveVariables() {
    try {
      await updateThemeVariables(this.selectedTheme.id, this.selectedThemeData.scssVariables);
      this.variables.message.title = localize({ key: 'design.themes.variables.success.title' });
      this.variables.message.content = localize({ key: 'design.themes.variables.success.message' });
      this.variables.message.hasError = false;
      this.variables.message.hasMessage = true;
      setTimeout(() => {
        this.variables.message.hasMessage = false;
      }, 30000);
    } catch (e) {
      this.variables.message.title = localize({ key: 'design.themes.variables.error.title' });
      this.variables.message.content = localize({ key: 'design.themes.variables.error.message' });
      this.variables.message.hasError = true;
      this.variables.message.hasMessage = true;
    }
  },
  async saveLinks() {
    const { modernPages, classicPages, forms, menus, galleries, files, blogCategories } = this.selectedThemeData;
    const { id } = this.selectedTheme;
    try {
      await Promise.all([
        ...Object.entries(modernPages).map(([name, link]) =>
          updateThemeModernPage(id, name, link === 'null' ? null : link),
        ),
        ...Object.entries(classicPages).map(([name, link]) =>
          updateThemeClassicPage(id, name, link === 'null' ? null : link),
        ),
        ...Object.entries(forms).map(([name, link]) => updateThemeForm(id, name, link === 'null' ? null : link)),
        ...Object.entries(menus).map(([name, link]) => updateThemeMenu(id, name, link === 'null' ? null : link)),
        ...Object.entries(galleries).map(([name, link]) => updateThemeGallery(id, name, link === 'null' ? null : link)),
        ...Object.entries(files).map(([name, link]) => updateThemeFile(id, name, link === 'null' ? null : link)),
        ...Object.entries(blogCategories).map(([name, link]) =>
          updateThemeBlogCategory(id, name, link === 'null' ? null : link),
        ),
      ]);

      this.links.message.title = localize({ key: 'design.themes.links.success.title' });
      this.links.message.content = localize({ key: 'design.themes.links.success.message' });
      this.links.message.hasError = false;
      this.links.message.hasMessage = true;
      setTimeout(() => {
        this.links.message.hasMessage = false;
      }, 30000);
    } catch (e) {
      this.links.message.title = localize({ key: 'design.themes.links.error.title' });
      this.links.message.content = localize({ key: 'design.themes.links.error.message' });
      this.links.message.hasError = true;
      this.links.message.hasMessage = true;
    }
  },
  async activate() {
    const confirmed = await confirm({
      title: localize({ key: 'design.themes.activate.title' }),
      message: localize({
        key: 'design.themes.activate.message',
        values: this.selectedTheme,
      }),
      approveLabel: localize({ key: 'design.themes.activate.approve' }),
      declineLabel: localize({ key: 'design.themes.activate.decline' }),
    });
    if (confirmed) {
      try {
        await activateTheme(this.selectedTheme.id);
        this.details.message.title = localize({ key: 'design.themes.activate.success.title' });
        this.details.message.content = localize({
          key: 'design.themes.activate.success.message',
          values: this.selectedTheme,
        });
        this.details.message.hasError = false;
        this.details.message.hasMessage = true;
        setTimeout(() => {
          this.details.message.hasMessage = false;
        }, 30000);
      } catch (e) {
        this.details.message.title = localize({ key: 'design.themes.activate.error.title' });
        this.details.message.content = localize({
          key: 'design.themes.activate.error.message',
          values: this.selectedTheme,
        });
        this.details.message.hasError = true;
        this.details.message.hasMessage = true;
      }
    }
  },
  async compileAssets() {
    const confirmed = await confirm({
      title: localize({ key: 'design.themes.assets.title' }),
      message: localize({
        key: 'design.themes.assets.message',
        values: this.selectedTheme,
      }),
      approveLabel: localize({ key: 'design.themes.assets.approve' }),
      declineLabel: localize({ key: 'design.themes.assets.decline' }),
    });
    if (confirmed) {
      try {
        await compileThemeAssets(this.selectedTheme.id);
        this.details.message.title = localize({ key: 'design.themes.assets.success.title' });
        this.details.message.content = localize({
          key: 'design.themes.assets.success.message',
          values: this.selectedTheme,
        });
        this.details.message.hasError = false;
        this.details.message.hasMessage = true;
        setTimeout(() => {
          this.details.message.hasMessage = false;
        }, 30000);
      } catch (e) {
        this.details.message.title = localize({ key: 'design.themes.assets.error.title' });
        this.details.message.content = localize({
          key: 'design.themes.assets.error.message',
          values: this.selectedTheme,
        });
        this.details.message.hasError = true;
        this.details.message.hasMessage = true;
      }
    }
  },
  async uploadTheme() {
    try {
      const theme = await uploadTheme(this.upload.file);
      this.themes.push(theme);
      await this.selectTheme(theme);
      this.upload.open = false;
    } catch (e) {
      this.upload.error.hasError = true;
      this.upload.error.title = localize({ key: 'design.themes.create.error.title' });
      if (e.status === 409) {
        this.upload.error.message = localize({ key: 'design.themes.create.error.conflict' });
      } else {
        this.upload.error.message = localize({ key: 'design.themes.create.error.generic' });
      }
      this.upload.error.hasError = true;
    }
  },
  async updateTheme() {
    try {
      await updateTheme(this.selectedTheme.id, this.edit.file);
      this.edit.open = false;
    } catch (e) {
      this.edit.error.hasError = true;
      this.edit.error.title = localize({ key: 'design.themes.edit.error.title' });
      this.edit.error.message = localize({ key: 'design.themes.edit.error.message' });
      this.edit.error.hasError = true;
    }
  },
  upload: {
    open: false,
    file: null,
    error: {
      title: '',
      message: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
    },
    selectFile(files) {
      if (files.length >= 1) {
        this.file = files.item(0);
      }
    },
  },
  edit: {
    open: false,
    title: '',
    error: {
      title: '',
      message: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
    },
    selectFile(files) {
      if (files.length >= 1) {
        this.file = files.item(0);
      }
    },
  },
  configuration: {
    message: {
      title: '',
      content: '',
      hasError: false,
      hasMessage: false,
    },
  },
  links: {
    message: {
      title: '',
      content: '',
      hasError: false,
      hasMessage: false,
    },
  },
  variables: {
    message: {
      title: '',
      content: '',
      hasError: false,
      hasMessage: false,
    },
  },
  details: {
    message: {
      title: '',
      content: '',
      hasError: false,
      hasMessage: false,
    },
  },
}));
