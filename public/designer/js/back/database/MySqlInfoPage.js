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
        content = html` <dl class="cosmo-list is--key-value">
          <dt>${localize({ key: 'database.mysql.server_type' })}</dt>
          <dd>${this.databaseInfo.server.comment}</dd>
          <dt>${localize({ key: 'database.mysql.server_version' })}</dt>
          <dd>${this.databaseInfo.server.version}</dd>
          <dt>${localize({ key: 'database.mysql.compile_machine' })}</dt>
          <dd>${this.databaseInfo.server.compileMachine}</dd>
          <dt>${localize({ key: 'database.mysql.compile_os' })}</dt>
          <dd>${this.databaseInfo.server.compileOs}</dd>
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
    document.querySelectorAll('.cosmo-side-list__item.is--active')
      .forEach((tag) => tag.classList.remove('is--active'));
    document.querySelector(`[data-tab="${this.selectedTab}"]`)
      .classList
      .add('is--active');

    const container = document.getElementById('mysql-content');
    clearChildren({ parent: container });
    container.innerHTML = content;
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html` <div class="cosmo-side-list">
      <nav class="cosmo-side-list__items">
        <a data-tab="server" class="cosmo-side-list__item is--active"
          >${localize({ key: 'database.mysql.system_and_server' })}</a
        >
        <a data-tab="global" class="cosmo-side-list__item">${localize({ key: 'database.mysql.global_variables' })}</a>
        <a data-tab="local" class="cosmo-side-list__item">${localize({ key: 'database.mysql.local_variables' })}</a>
        <a data-tab="session" class="cosmo-side-list__item">${localize({ key: 'database.mysql.session_variables' })}</a>
      </nav>
      <div class="cosmo-side-list__content" id="mysql-content"></div>
    </div>`;
  }

  async displayed() {
    await super.displayed();
    this.databaseInfo = await get('/api/maintenance/database/analyze');
    this.displaySelectedTab();
  }

  bindEvents() {
    super.bindEvents();
    document.querySelectorAll('.cosmo-side-list__item')
      .forEach((item) =>
        item.addEventListener('click', (e) => {
          e.preventDefault();
          this.selectedTab = e.target.getAttribute('data-tab');
          this.displaySelectedTab();
        }),
      );
  }
}
