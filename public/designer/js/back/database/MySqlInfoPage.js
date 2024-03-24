import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';

export default class MySqlInfoPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.databaseInfo = {};
    this.selectedTab = 'server';
  }

  displaySelectedTab() {
    const displayVariables = (variables) =>
      html` <table class="cosmo-table">
        <thead>
          <tr>
            <th>${localize({ key: 'database.mysql.name' })}</th>
            <th>${localize({ key: 'database.mysql.value' })}</th>
          </tr>
        </thead>
        <tbody>
          ${Object.keys(variables)
        .map(
          (variable) =>
            html` <tr>
                <td>${variable}</td>
                <td>${variables[variable]}</td>
              </tr>`,
        )}
        </tbody>
      </table>`;
    let content = '';
    switch (this.selectedTab) {
      case 'server':
        content = html` <dl class="cosmo-key-value-list">
          <dt class="cosmo-key-value-list__key">${localize({ key: 'database.mysql.server_type' })}</dt>
          <dd class="cosmo-key-value-list__value">${this.databaseInfo.server.comment}</dd>
          <dt class="cosmo-key-value-list__key">${localize({ key: 'database.mysql.server_version' })}</dt>
          <dd class="cosmo-key-value-list__value">${this.databaseInfo.server.version}</dd>
          <dt class="cosmo-key-value-list__key">${localize({ key: 'database.mysql.compile_machine' })}</dt>
          <dd class="cosmo-key-value-list__value">${this.databaseInfo.server.compileMachine}</dd>
          <dt class="cosmo-key-value-list__key">${localize({ key: 'database.mysql.compile_os' })}</dt>
          <dd class="cosmo-key-value-list__value">${this.databaseInfo.server.compileOs}</dd>
        </dl>`;
        break;
      case 'global':
        content = displayVariables(this.databaseInfo.variables.global);
        break;
      case 'local':
        content = displayVariables(this.databaseInfo.variables.local);
        break;
      case 'session':
        content = displayVariables(this.databaseInfo.variables.session);
        break;
      default:
        break;
    }
    document
      .querySelectorAll('.cosmo-list__item--active')
      .forEach((tag) => tag.classList.remove('cosmo-list__item--active'));
    document.querySelector(`[data-tab="${this.selectedTab}"]`)
      .classList
      .add('cosmo-list__item--active');

    const container = document.getElementById('mysql-content');
    clearChildren({ parent: container });
    container.innerHTML = content;
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html` <div class="cosmo-list">
      <nav class="cosmo-list__items">
        <a data-tab="server" class="cosmo-list__item cosmo-list__item--active"
          >${localize({
      key: 'database.mysql.system_and_server',
    })}</a
        >
        <a data-tab="global" class="cosmo-list__item">${localize({ key: 'database.mysql.global_variables' })}</a>
        <a data-tab="local" class="cosmo-list__item">${localize({ key: 'database.mysql.local_variables' })}</a>
        <a data-tab="session" class="cosmo-list__item">${localize({ key: 'database.mysql.session_variables' })}</a>
      </nav>
      <div class="cosmo-list__content" id="mysql-content"></div>
    </div>`;
  }

  async displayed() {
    await super.displayed();
    this.databaseInfo = await get('/api/maintenance/database/analyze');
    this.displaySelectedTab();
  }

  bindEvents() {
    super.bindEvents();
    document.querySelectorAll('.cosmo-list__item')
      .forEach((item) =>
        item.addEventListener('click', (e) => {
          e.preventDefault();
          this.selectedTab = e.target.getAttribute('data-tab');
          this.displaySelectedTab();
        }),
      );
  }
}
