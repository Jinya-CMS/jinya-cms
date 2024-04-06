import { Alpine } from '../../../../lib/alpine.js';
import { getClassicPages } from '../../foundation/api/classic-pages.js';
import {
  getTheme,
  getThemeBlogCategories, getThemeClassicPages,
  getThemeConfigurationStructure,
  getThemeDefaultConfiguration, getThemeFiles, getThemeForms, getThemeGalleries, getThemeMenus, getThemeModernPages,
  getThemes, getThemeStyleVariables, updateThemeConfiguration, updateThemeVariables,
} from '../../foundation/api/themes.js';
import { getForms } from '../../foundation/api/forms.js';
import { getGalleries } from '../../foundation/api/galleries.js';
import { getFiles } from '../../foundation/api/files.js';
import { getModernPages } from '../../foundation/api/modern-pages.js';
import { getMenus } from '../../foundation/api/menus.js';
import { getBlogCategories } from '../../foundation/api/blog-categories.js';
import localize from '../../foundation/localize.js';

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
    this.create.error.reset();
    this.create.title = '';
    this.create.open = true;
  },
  openEditDialog() {
    this.edit.error.reset();
    this.edit.title = this.selectedPage.title;
    this.edit.open = true;
  },
  loadThemeConfiguration() {
    const configuration = Array.isArray(this.selectedTheme.configuration) ? {} : this.selectedTheme.configuration;

    for (const group of this.selectedThemeData.configurationStructure.groups) {
      configuration[group.name] = configuration[group.name] ?? {};
    }

    this.selectedThemeData.configuration = configuration;
  },
  loadThemeVariables() {
    this.selectedThemeData.scssVariables = Array.isArray(this.selectedTheme.scssVariables) ? {} : this.selectedTheme.scssVariables;
  },
  async loadLinks() {
    const [
      modernPages,
      classicPages,
      forms,
      menus,
      galleries,
      files,
      blogCategories,
    ] = await Promise.all([
      getThemeModernPages(this.selectedTheme.id),
      getThemeClassicPages(this.selectedTheme.id),
      getThemeForms(this.selectedTheme.id),
      getThemeMenus(this.selectedTheme.id),
      getThemeGalleries(this.selectedTheme.id),
      getThemeFiles(this.selectedTheme.id),
      getThemeBlogCategories(this.selectedTheme.id),
    ]);

    this.selectedThemeData.modernPages = modernPages;
    this.selectedThemeData.classicPages = classicPages;
    this.selectedThemeData.forms = forms;
    this.selectedThemeData.menus = menus;
    this.selectedThemeData.galleries = galleries;
    this.selectedThemeData.files = files;
    this.selectedThemeData.blogCategories = blogCategories;
  },
  async loadConfigurationStructure() {
    const [
      styleVariables,
      defaultConfiguration,
      configurationStructure,
    ] = await Promise.all([
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
      const [
        galleries,
        files,
        classicPages,
        modernPages,
        menus,
        blogCategories,
        forms,
      ] = await Promise.all([
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
  create: {
    open: false,
    title: '',
    error: {
      title: '',
      message: '',
      tagError: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
    },
  },
  edit: {
    open: false,
    title: '',
    error: {
      title: '',
      message: '',
      tagError: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
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
}));
