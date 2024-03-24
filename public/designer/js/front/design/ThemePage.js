import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, put } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';

export default class ThemePage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.themes = [];
    this.selectedTheme = null;
    this.configurationStructure = {};

    this.themeSegmentPages = {};
    this.themePages = {};
    this.themeForms = {};
    this.themeMenus = {};
    this.themeGalleries = {};
    this.themeFiles = {};
    this.themeCategories = {};

    this.segmentPages = [];
    this.pages = [];
    this.forms = [];
    this.menus = [];
    this.galleries = [];
    this.files = [];
    this.categories = [];

    this.defaultVariables = {};
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
      <div class="cosmo-side-list">
        <nav class="cosmo-side-list__items" id="theme-list"></nav>
        <div class="cosmo-side-list__content jinya-designer">
          <div class="jinya-designer__title">
            <span class="cosmo-title" id="theme-title"></span>
          </div>
          <div class="cosmo-toolbar cosmo-toolbar--designer">
            <div class="cosmo-toolbar__group">
              <button id="activate-theme" class="cosmo-button">
                ${localize({ key: 'design.themes.action.activate' })}
              </button>
              <button id="compile-assets" class="cosmo-button">
                ${localize({ key: 'design.themes.action.compile_assets' })}
              </button>
              <button id="update-theme" class="cosmo-button">
                ${localize({ key: 'design.themes.action.update' })}
              </button>
            </div>
          </div>
          <div class="jinya-designer__content jinya-designer__content--theme">
            <div class="cosmo-tab cosmo-tab-control--theme">
              <div class="cosmo-tab__links">
                <a
                  id="details-tab-link"
                  data-type="tab"
                  data-target="details-tab"
                  class="cosmo-tab__link is--active"
                >
                  ${localize({ key: 'design.themes.tabs.details' })}
                </a>
                <a
                  id="configuration-tab-link"
                  data-type="tab"
                  data-target="configuration-tab"
                  class="cosmo-tab__link"
                >
                  ${localize({ key: 'design.themes.tabs.configuration' })}
                </a>
                <a id="links-tab-link" data-type="tab" data-target="links-tab" class="cosmo-tab__link">
                  ${localize({ key: 'design.themes.tabs.links' })}
                </a>
                <a
                  id="variables-tab-link"
                  data-type="tab"
                  data-target="variables-tab"
                  class="cosmo-tab__link"
                >
                  ${localize({ key: 'design.themes.tabs.variables' })}
                </a>
              </div>
              <div
                id="details-tab"
                class="cosmo-tab__content cosmo-tab-control__content--details cosmo-tab-control__content--theme"
              >
                <img class="jinya-theme-details__preview" alt="" src="" />
                <div class="jinya-theme-details__description"></div>
              </div>
              <form
                class="cosmo-tab__content cosmo-tab-control__content--theme"
                style="display: none;"
                id="configuration-tab"
              >
                <div class="cosmo-input__group"></div>
                <div class="cosmo-button__container jinya-sticky--bottom">
                  <button type="reset" class="cosmo-button">
                    ${localize({ key: 'design.themes.configuration.discard' })}
                  </button>
                  <button type="submit" class="cosmo-button is--primary">
                    ${localize({ key: 'design.themes.configuration.save' })}
                  </button>
                </div>
              </form>
              <form
                class="cosmo-tab__content cosmo-tab-control__content--theme"
                style="display: none;"
                id="links-tab"
              >
                <div class="cosmo-input__group"></div>
                <div class="cosmo-button__container jinya-sticky--bottom">
                  <button type="reset" class="cosmo-button">${localize({ key: 'design.themes.links.discard' })}</button>
                  <button type="submit" class="cosmo-button is--primary">
                    ${localize({ key: 'design.themes.links.save' })}
                  </button>
                </div>
              </form>
              <form
                class="cosmo-tab__content cosmo-tab-control__content--theme"
                style="display: none;"
                id="variables-tab"
              >
                <div class="cosmo-input__group"></div>
                <div class="cosmo-button__container jinya-sticky--bottom">
                  <button type="reset" class="cosmo-button">
                    ${localize({ key: 'design.themes.variables.discard' })}
                  </button>
                  <button type="submit" class="cosmo-button is--primary">
                    ${localize({ key: 'design.themes.variables.save' })}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  displayThemes() {
    let list = '';
    for (const theme of this.themes) {
      list += `<a class="cosmo-side-list__item" data-id="${theme.id}">${theme.displayName}</a>`;
    }
    clearChildren({ parent: document.getElementById('theme-list') });
    document.getElementById('theme-list').innerHTML = `${list}
                <button id="upload-theme-button" class="cosmo-button is--full-width">
                    ${localize({ key: 'design.themes.action.create' })}
                </button>`;
    document.querySelectorAll('.cosmo-side-list__item')
      .forEach((item) => {
        item.addEventListener('click', async () => {
          this.selectTheme({ id: item.getAttribute('data-id') });
          await this.displaySelectedTheme();
        });
      });
    document.getElementById('upload-theme-button')
      .addEventListener('click', async () => {
        const { default: UploadThemeDialog } = await import('./themes/UploadThemeDialog.js');
        const dialog = new UploadThemeDialog({
          onHide: async ({ name }) => {
            const { items } = await get('/api/theme');
            this.themes = items;
            this.displayThemes();
            const selectedTheme = this.themes.find((t) => t.name === name);
            this.selectTheme({ id: selectedTheme.id });
            await this.displaySelectedTheme();
          },
        });
        await dialog.show();
      });
  }

  selectTheme({ id }) {
    this.selectedTheme = this.themes.find((p) => p.id === parseInt(id, 10));
    document
      .querySelectorAll('.cosmo-side-list__item.is--active')
      .forEach((item) => item.classList.remove('is--active'));
    document.querySelector(`[data-id="${id}"]`)
      .classList
      .add('is--active');
  }

  // eslint-disable-next-line class-methods-use-this
  getValueForCurrentLanguage(data) {
    const currentLanguage = navigator.language;
    // eslint-disable-next-line no-prototype-builtins
    if (data.hasOwnProperty(currentLanguage)) {
      return data[currentLanguage];
    }
    // eslint-disable-next-line no-prototype-builtins
    if (data.hasOwnProperty('en')) {
      return data.en;
    }

    return data;
  }

  async displaySelectedTheme() {
    document.getElementById('theme-title').innerText = this.selectedTheme.displayName;
    const img = document.querySelector('#details-tab img');
    img.src = `/api/theme/${this.selectedTheme.id}/preview`;
    img.alt = localize({ key: 'design.themes.details.preview' });

    let defaultConfiguration = {};

    await Promise.all([get(`/api/theme/${this.selectedTheme.id}/segment-page`)
      .then((result) => {
        for (const key of Object.keys(result)) {
          this.themeSegmentPages[key] = result[key].id;
        }
      }), get(`/api/theme/${this.selectedTheme.id}/page`)
      .then((result) => {
        for (const key of Object.keys(result)) {
          this.themePages[key] = result[key].id;
        }
      }), get(`/api/theme/${this.selectedTheme.id}/form`)
      .then((result) => {
        for (const key of Object.keys(result)) {
          this.themeForms[key] = result[key].id;
        }
      }), get(`/api/theme/${this.selectedTheme.id}/menu`)
      .then((result) => {
        for (const key of Object.keys(result)) {
          this.themeMenus[key] = result[key].id;
        }
      }), get(`/api/theme/${this.selectedTheme.id}/gallery`)
      .then((result) => {
        for (const key of Object.keys(result)) {
          this.themeGalleries[key] = result[key].id;
        }
      }), get(`/api/theme/${this.selectedTheme.id}/file`)
      .then((result) => {
        for (const key of Object.keys(result)) {
          this.themeFiles[key] = result[key].id;
        }
      }), get(`/api/theme/${this.selectedTheme.id}/category`)
      .then((result) => {
        for (const key of Object.keys(result)) {
          this.themeCategories[key] = result[key].id;
        }
      }), get(`/api/theme/${this.selectedTheme.id}/styling`)
      .then((result) => {
        this.defaultVariables = result;
      }), get(`/api/theme/${this.selectedTheme.id}/configuration/default`)
      .then((result) => {
        defaultConfiguration = result;
      }), get(`/api/theme/${this.selectedTheme.id}/configuration/structure`)
      .then((result) => {
        this.configurationStructure = result;
      })]);

    const {
      configuration,
      scssVariables,
    } = this.selectedTheme;
    Object.keys(defaultConfiguration)
      .forEach((item) => {
        configuration[item] = configuration[item] ?? {};
      });

    const getGroupHeader = (group) => html`
      <span class="cosmo-input__header"> ${this.getValueForCurrentLanguage(group.title)} </span>`;
    const getField = (group, field) => {
      switch (field.type) {
        case 'boolean':
          return html`
            <div class="cosmo-input__group is--checkbox">
              <input
                ${configuration[group.name][field.name] ? 'checked' : ''}
                type="checkbox"
                class="cosmo-checkbox"
                id="${group.name}__%__${field.name}"
                name="${group.name}__%__${field.name}"
              />
              <label for="${group.name}__%__${field.name}">${this.getValueForCurrentLanguage(field.label)}</label>
            </div>`;
        default:
          return html`
            <label class="cosmo-label" for="${group.name}__%__${field.name}">
              ${this.getValueForCurrentLanguage(field.label)}
            </label>
            <input
              id="${group.name}__%__${field.name}"
              name="${group.name}__%__${field.name}"
              class="cosmo-input"
              type="text"
              placeholder="${defaultConfiguration[group.name][field.name]}"
              value="${configuration[group.name][field.name] ?? ''}"
            />`;
      }
    };
    const getGroups = () => this.configurationStructure.groups
      .map((group) => getGroupHeader(group) + group.fields.map((field) => getField(group, field))
        .join('\n'))
      .join('\n');
    const configurationElement = document.querySelector('#configuration-tab .cosmo-input__group');
    clearChildren({ parent: configurationElement });
    configurationElement.innerHTML = getGroups();

    const getFileLinks = () => html`
      <span
        class="cosmo-input__header">${localize({ key: 'design.themes.links.files' })}</span>` + Object.keys(this.configurationStructure.links.files)
      .map((link) => html`
        <label for="files_${link}"
               class="cosmo-label">${this.getValueForCurrentLanguage(this.configurationStructure.links.files[link])}</label>
        <select required id="files_${link}" name="file__%__${link}" class="cosmo-select">
          ${this.files.map((file) => `<option ${file.id === this.themeFiles[link] ? 'selected' : ''} value="${file.id}">${file.name}</option>`)}
        </select>`)
      .join('\n');
    const getSegmentPageLinks = () => html`
      <span
        class="cosmo-input__header">${localize({ key: 'design.themes.links.segment_pages' })}</span>` + Object.keys(this.configurationStructure.links.segment_pages)
      .map((link) => html`
        <label for="segment_pages_${link}"
               class="cosmo-label">${this.getValueForCurrentLanguage(this.configurationStructure.links.segment_pages[link])}</label>
        <select required id="segment_pages_${link}" name="segment-page__%__${link}" class="cosmo-select">
          ${this.segmentPages.map((segmentPage) => `<option ${segmentPage.id === this.themeSegmentPages[link] ? 'selected' : ''} value="${segmentPage.id}">${segmentPage.name}</option>`)}
        </select>`)
      .join('\n');
    const getPageLinks = () => html`
      <span
        class="cosmo-input__header">${localize({ key: 'design.themes.links.pages' })}</span>` + Object.keys(this.configurationStructure.links.pages)
      .map((link) => html`
        <label for="pages_${link}"
               class="cosmo-label">${this.getValueForCurrentLanguage(this.configurationStructure.links.pages[link])}</label>
        <select required id="pages_${link}" name="page__%__${link}" class="cosmo-select">
          ${this.pages.map((page) => `<option ${page.id === this.themePages[link] ? 'selected' : ''} value="${page.id}">${page.title}</option>`)}
        </select>`)
      .join('\n');
    const getFormLinks = () => html`
      <span
        class="cosmo-input__header">${localize({ key: 'design.themes.links.forms' })}</span>` + Object.keys(this.configurationStructure.links.forms)
      .map((link) => html`
        <label for="forms_${link}"
               class="cosmo-label">${this.getValueForCurrentLanguage(this.configurationStructure.links.forms[link])}</label>
        <select required id="forms_${link}" name="form__%__${link}" class="cosmo-select">
          ${this.forms.map((form) => `<option ${form.id === this.themeForms[link] ? 'selected' : ''} value="${form.id}">${form.title}</option>`)}
        </select>`)
      .join('\n');
    const getMenuLinks = () => html`
      <span
        class="cosmo-input__header">${localize({ key: 'design.themes.links.menus' })}</span>` + Object.keys(this.configurationStructure.links.menus)
      .map((link) => html`
        <label for="menus_${link}"
               class="cosmo-label">${this.getValueForCurrentLanguage(this.configurationStructure.links.menus[link])}</label>
        <select required id="menus_${link}" name="menu__%__${link}" class="cosmo-select">
          ${this.menus.map((menu) => `<option ${menu.id === this.themeMenus[link] ? 'selected' : ''} value="${menu.id}">${menu.name}</option>`)}
        </select>`)
      .join('\n');
    const getGalleryLinks = () => html`
      <span
        class="cosmo-input__header">${localize({ key: 'design.themes.links.galleries' })}</span>` + Object.keys(this.configurationStructure.links.galleries)
      .map((link) => html`
        <label for="galleries_${link}"
               class="cosmo-label">${this.getValueForCurrentLanguage(this.configurationStructure.links.galleries[link])}</label>
        <select required id="galleries_${link}" name="gallery__%__${link}" class="cosmo-select">
          ${this.galleries.map((gallery) => `<option ${gallery.id === this.themeGalleries[link] ? 'selected' : ''} value="${gallery.id}">${gallery.name}</option>`)}
        </select>`)
      .join('\n');
    const getCategoryLinks = () => html`
      <span
        class="cosmo-input__header">${localize({ key: 'design.themes.links.categories' })}</span>` + Object.keys(this.configurationStructure.links.blog_categories)
      .map((link) => html`
        <label for="categories_${link}"
               class="cosmo-label">${this.getValueForCurrentLanguage(this.configurationStructure.links.blog_categories[link])}</label>
        <select required id="categories_${link}" name="category__%__${link}" class="cosmo-select">
          ${this.blog_categories.map((category) => `<option ${category.id === this.themeCategories[link] ? 'selected' : ''} value="${category.id}">${category.name}</option>`)}
        </select>`)
      .join('\n');

    let links = '';
    if (this.configurationStructure.links.files) {
      links += getFileLinks();
    }
    if (this.configurationStructure.links.pages) {
      links += getPageLinks();
    }
    if (this.configurationStructure.links.menus) {
      links += getMenuLinks();
    }
    if (this.configurationStructure.links.galleries) {
      links += getGalleryLinks();
    }
    if (this.configurationStructure.links.blog_categories) {
      links += getCategoryLinks();
    }
    if (this.configurationStructure.links.segment_pages) {
      links += getSegmentPageLinks();
    }
    if (this.configurationStructure.links.forms) {
      links += getFormLinks();
    }

    const linkElement = document.querySelector('#links-tab .cosmo-input__group');
    clearChildren({ parent: linkElement });
    linkElement.innerHTML = links;

    const getVariables = () => Object.keys(this.defaultVariables)
      .map((variable) => html`
        <label for="${variable}">${variable}</label>
        <input
          id="${variable}"
          name="${variable}"
          placeholder="${this.defaultVariables[variable]}"
          type="text"
          class="cosmo-input"
          value="${scssVariables[variable] ?? ''}"
        />`)
      .join('\n');
    const varElement = document.querySelector('#variables-tab .cosmo-input__group');
    clearChildren({ parent: varElement });
    varElement.innerHTML = getVariables();
  }

  async displayed() {
    await super.displayed();
    await this.loadThemes();
    this.displayThemes();
    this.selectTheme({ id: this.themes[0].id });
    await Promise.all([get('/api/segment-page')
      .then(({ items }) => {
        this.segmentPages = items;
      }), get('/api/page')
      .then(({ items }) => {
        this.pages = items;
      }), get('/api/form')
      .then(({ items }) => {
        this.forms = items;
      }), get('/api/menu')
      .then(({ items }) => {
        this.menus = items;
      }), get('/api/media/gallery')
      .then(({ items }) => {
        this.galleries = items;
      }), get('/api/media/file')
      .then(({ items }) => {
        this.files = items;
      }), get('/api/blog/category')
      .then(({ items }) => {
        this.categories = items;
      })]);
    await this.displaySelectedTheme();
  }

  async loadThemes() {
    const { items: themes } = await get('/api/theme');
    this.themes = themes;
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('activate-theme')
      .addEventListener('click', async () => {
        if (await confirm({
          title: localize({ key: 'design.themes.activate.title' }),
          message: localize({
            key: 'design.themes.activate.message',
            values: this.selectedTheme,
          }),
          approveLabel: localize({ key: 'design.themes.activate.approve' }),
          declineLabel: localize({ key: 'design.themes.activate.decline' }),
        })) {
          try {
            await put(`/api/theme/${this.selectedTheme.id}/active`);
            await alert({
              title: localize({ key: 'design.themes.activate.success.title' }),
              message: localize({
                key: 'design.themes.activate.success.message',
                values: this.selectedTheme,
              }),
            });
          } catch (e) {
            await alert({
              title: localize({ key: 'design.themes.activate.error.title' }),
              message: localize({
                key: 'design.themes.activate.error.message',
                values: this.selectedTheme,
              }),
            });
          }
        }
      });
    document.getElementById('compile-assets')
      .addEventListener('click', async () => {
        if (await confirm({
          title: localize({ key: 'design.themes.assets.title' }),
          message: localize({
            key: 'design.themes.assets.message',
            values: this.selectedTheme,
          }),
          approveLabel: localize({ key: 'design.themes.assets.approve' }),
          declineLabel: localize({ key: 'design.themes.assets.decline' }),
        })) {
          try {
            await put(`/api/theme/${this.selectedTheme.id}/assets`);
            await alert({
              title: localize({ key: 'design.themes.assets.success.title' }),
              message: localize({
                key: 'design.themes.assets.success.message',
                values: this.selectedTheme,
              }),
            });
          } catch (e) {
            await alert({
              title: localize({ key: 'design.themes.assets.error.title' }),
              message: localize({
                key: 'design.themes.assets.error.message',
                values: this.selectedTheme,
              }),
            });
          }
        }
      });
    document.getElementById('update-theme')
      .addEventListener('click', async () => {
        const { default: UploadThemeDialog } = await import('./themes/UpdateThemeDialog.js');
        const dialog = new UploadThemeDialog({
          id: this.selectedTheme.id,
          onHide: async () => {
            const { items } = await get('/api/theme');
            this.themes = items;
            this.selectTheme(this.themes.filter((f) => f.id === this.selectedTheme.id)[0]);
            await this.displaySelectedTheme();
          },
        });
        dialog.show();
      });

    document.getElementById('configuration-tab')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const configuration = {};
        const formData = new FormData(document.getElementById('configuration-tab'));
        for (const [key, value] of formData.entries()) {
          const split = key.split('__%__');
          const group = split[0];
          const field = split[1];
          configuration[group] = configuration[group] ?? {};
          if (this.configurationStructure.groups.find((g) => g.name === group)
            .fields
            .find((f) => f.name === field).type === 'boolean') {
            configuration[group][field] = value.toLowerCase() === 'on';
          } else {
            configuration[group][field] = value;
          }
        }

        try {
          await put(`/api/theme/${this.selectedTheme.id}/configuration`, { configuration });
          await alert({
            title: localize({ key: 'design.themes.configuration.success.title' }),
            message: localize({ key: 'design.themes.configuration.success.message' }),
          });
          await this.loadThemes();
          this.displayThemes();
          this.selectTheme({ id: this.selectedTheme.id });
          await this.displaySelectedTheme();
        } catch (err) {
          await alert({
            title: localize({ key: 'design.themes.configuration.error.title' }),
            message: localize({ key: 'design.themes.configuration.error.message' }),
          });
        }
      });
    document.getElementById('links-tab')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(document.getElementById('links-tab'));
        const promises = [];
        for (const [key, value] of formData.entries()) {
          const split = key.split('__%__');
          const type = split[0];
          const field = split[1];
          const data = {};
          if (type === 'segment-page') {
            data.segmentPage = value;
          } else {
            data[type] = value;
          }
          promises.push(put(`/api/theme/${this.selectedTheme.id}/${type}/${field}`, data));
        }

        try {
          await Promise.all(promises);
          await alert({
            title: localize({ key: 'design.themes.links.success.title' }),
            message: localize({ key: 'design.themes.links.success.message' }),
          });
          await this.loadThemes();
          this.displayThemes();
          this.selectTheme({ id: this.selectedTheme.id });
          await this.displaySelectedTheme();
        } catch (err) {
          await alert({
            title: localize({ key: 'design.themes.links.error.title' }),
            message: localize({ key: 'design.themes.links.error.message' }),
          });
        }
      });
    document.getElementById('variables-tab')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(document.getElementById('variables-tab'));
        const variables = {};
        for (const [key, value] of formData.entries()) {
          if (value !== '') {
            variables[key] = value;
          }
        }

        try {
          await put(`/api/theme/${this.selectedTheme.id}/styling`, { variables });
          await alert({
            title: localize({ key: 'design.themes.variables.success.title' }),
            message: localize({ key: 'design.themes.variables.success.message' }),
          });
          await this.loadThemes();
          this.displayThemes();
          this.selectTheme({ id: this.selectedTheme.id });
          await this.displaySelectedTheme();
        } catch (err) {
          await alert({
            title: localize({ key: 'design.themes.variables.error.title' }),
            message: localize({ key: 'design.themes.variables.error.message' }),
          });
        }
      });

    document.querySelectorAll('[data-type="tab"]')
      .forEach((tab) => {
        tab.addEventListener('click', (e) => {
          e.preventDefault();
          const target = tab.getAttribute('data-target');
          document
            .querySelectorAll('[data-type="tab"]')
            .forEach((item) => item.classList.remove('is--active'));
          document.querySelectorAll('.cosmo-tab__content')
            .forEach((item) => {
              // eslint-disable-next-line no-param-reassign
              item.style.display = 'none';
            });
          document.getElementById(target)
            .removeAttribute('style');
          tab.classList.add('is--active');
        });
      });
  }
}
