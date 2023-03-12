import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';

export default class PhpInfoPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.phpInfo = [];
    this.selectedExtension = null;
    this.selectedExtensionName = '';
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
        <div class="cosmo-list">
            <nav class="cosmo-list__items" id="extension-list">
            </nav>
            <div class="cosmo-list__content" id="php-info-content">
            </div>
        </div>`;
  }

  selectExtension({ name }) {
    this.selectedExtensionName = name;
    if (name === 'system-environment') {
      this.selectedExtension = null;
    } else {
      this.selectedExtension = this.phpInfo.extensions[name];
    }
  }

  displaySelectedExtension() {
    let content = '';
    if (this.selectedExtension) {
      content = html`
          <span class="cosmo-title">${this.selectedExtensionName}</span>
          <dl class="cosmo-key-value-list">
              ${Object.keys(this.selectedExtension).map((key) => {
                  if (key === 'ini') {
                      return '';
                  }
                  return html`
                      <dt class="cosmo-key-value-list__key">${key}</dt>
                      <dd class="cosmo-key-value-list__value">${this.selectedExtension[key]}</dd>`;
              })}
          </dl>
          ${Object.keys(this.selectedExtension.ini).length > 0 ? html`
              <h2>${localize({ key: 'maintenance.php.extension.ini_values' })}</h2>
              <table class="cosmo-table">
                  <thead>
                  <tr>
                      <th>${localize({ key: 'maintenance.php.ini.name' })}</th>
                      <th>${localize({ key: 'maintenance.php.ini.value' })}</th>
                  </tr>
                  </thead>
                  <tbody>
                  ${Object.keys(this.selectedExtension.ini).map((key) => html`
                      <tr>
                          <td>${key}</td>
                          <td>${this.selectedExtension.ini[key]}</td>
                      </tr>`)}
                  </tbody>
              </table>
          ` : ''}`;
    } else {
      content = html`
          <dl class="cosmo-key-value-list">
              <span class="cosmo-title">${localize({ key: 'maintenance.php.system_and_server' })}</span>
              <h2>${localize({ key: 'maintenance.php.about' })}</h2>
              ${Object.keys(this.phpInfo.about).map((key) => html`
                  <dt class="cosmo-key-value-list__key">${key}</dt>
                  <dd class="cosmo-key-value-list__value">${this.phpInfo.about[key]}</dd>`)}
              ${this.phpInfo.apache ? html`
                  <h2>${localize({ key: 'maintenance.php.apache' })}</h2>
                  <dl class="cosmo-key-value-list">
                      ${Object.keys(this.phpInfo.apache).map((key) => html`
                          <dt class="cosmo-key-value-list__key">${key}</dt>
                          <dd class="cosmo-key-value-list__value">${this.phpInfo.apache[key]}</dd>`)}
                  </dl>` : ''}
          </dl>`;
    }

    clearChildren({ parent: document.getElementById('php-info-content') });
    document.getElementById('php-info-content').innerHTML = content;
    document
      .querySelectorAll('.cosmo-list__item--active')
      .forEach((item) => item.classList.remove('cosmo-list__item--active'));
    document
      .querySelector(`.cosmo-list__item[data-name="${this.selectedExtensionName}"]`)
      .classList.add('cosmo-list__item--active');
  }

  displayExtensions() {
    let list = html`
        <a data-name="system-environment"
           class="cosmo-list__item">${localize({ key: 'maintenance.php.system_and_server' })}</a>`;
    for (const extension of Object.keys(this.phpInfo.extensions)) {
      list += `<a class="cosmo-list__item" data-name="${extension}">${extension}</a>`;
    }
    clearChildren({ parent: document.getElementById('extension-list') });
    document.getElementById('extension-list').innerHTML = list;
    document.querySelectorAll('.cosmo-list__item').forEach((item) => {
      item.addEventListener('click', async () => {
        this.selectExtension({ name: item.getAttribute('data-name') });
        this.displaySelectedExtension();
      });
    });
  }

  async displayed() {
    await super.displayed();
    this.phpInfo = await get('/api/phpinfo/full');
    this.displayExtensions();
    this.selectExtension({ name: 'system-environment' });
    this.displaySelectedExtension();
  }

  bindEvents() {
    super.bindEvents();
  }
}
