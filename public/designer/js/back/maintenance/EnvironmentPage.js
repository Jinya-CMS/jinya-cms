import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';

export default class EnvironmentPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.environment = [];
  }

  displayEnvironment() {
    const table = document.querySelector('.cosmo-table tbody');
    clearChildren({ parent: table });
    for (const { key, value } of this.environment) {
      const tr = document.createElement('tr');
      tr.innerHTML = html` <tr>
        <td>${key}</td>
        <td>${value}</td>
      </tr>`;
      table.append(tr);
    }
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html` <table class="cosmo-table">
      <thead>
        <tr>
          <th>${localize({ key: 'maintenance.configuration.key' })}</th>
          <th>${localize({ key: 'maintenance.configuration.value' })}</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>`;
  }

  async displayed() {
    await super.displayed();
    this.environment = await get('/api/environment');
    this.displayEnvironment();
  }
}
