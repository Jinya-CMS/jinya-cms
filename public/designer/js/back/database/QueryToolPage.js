import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { post } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';

export default class QueryToolPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.editor = null;
    this.queryResult = [];
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html` <div class="jinya-horizontal-split">
      <div class="jinya-code-editor__container">
        <div class="jinya-code-editor"></div>
        <div class="cosmo-button__container jinya-code-editor__execute">
          <button class="cosmo-button" id="execute">${localize({ key: 'database.query_tool.execute' })}</button>
        </div>
      </div>
      <div class="jinya-code-editor__result">
        <div class="cosmo-tab-control cosmo-tab-control--query-tool"></div>
      </div>
    </div>`;
  }

  async displayed() {
    await super.displayed();
    this.editor = monaco.editor.create(document.querySelector('.jinya-code-editor'), {
      language: 'mysql',
      theme: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'hc-black' : 'vs',
    });
  }

  displayQueryResult() {
    const container = document.querySelector('.cosmo-tab-control--query-tool');
    clearChildren({ parent: container });
    const tabs = document.createElement('div');
    tabs.classList.add('cosmo-tab-control__tabs');
    container.append(tabs);

    let isFirst = true;
    for (const result of this.queryResult) {
      const tabElem = document.createElement('a');
      tabElem.classList.add('cosmo-tab-control__tab-link');
      if (isFirst) {
        tabElem.classList.add('cosmo-tab-control__tab-link--active');
      }
      tabElem.innerText = result.statement;
      tabElem.addEventListener('click', () => {
        document
          .querySelectorAll('.cosmo-tab-control__tab-link--active')
          .forEach((item) => item.classList.remove('cosmo-tab-control__tab-link--active'));
        document.querySelectorAll('.cosmo-tab-control__content')
          .forEach((item) => {
            // eslint-disable-next-line no-param-reassign
            item.style.display = 'none';
          });
        tabElem.classList.add('cosmo-tab-control__tab-link--active');
        document.getElementById(result.statement).style.display = 'flex';
      });
      tabs.append(tabElem);

      const contentElem = document.createElement('div');
      container.append(contentElem);
      contentElem.classList.add('cosmo-tab-control__content');
      if (isFirst) {
        contentElem.style.display = 'flex';
      } else {
        contentElem.style.display = 'none';
      }
      contentElem.id = result.statement;
      if (result.result.length === 0) {
        contentElem.innerText = localize({ key: 'database.query_tool.no_result' });
      } else if (result.result.length > 0) {
        const keys = Object.keys(result.result[0]);
        contentElem.innerHTML = html` <table class="cosmo-table">
          <thead>
            <tr>
              ${keys.map((key) => `<th>${key}</th>`)}
            </tr>
          </thead>
          <tbody></tbody>
        </table>`;
        for (const row of result.result) {
          const tr = document.createElement('tr');
          for (const key of keys) {
            const td = document.createElement('td');
            td.innerText = row[key];
            tr.append(td);
          }
          contentElem.querySelector('tbody')
            .append(tr);
        }
      } else if (!Number.isNaN(result.result)) {
        contentElem.innerText = localize({
          key: 'database.query_tool.rows_affected',
          values: { count: result.result },
        });
      }
      isFirst = false;
    }
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('execute')
      .addEventListener('click', async () => {
        const query = this.editor.getValue();
        try {
          this.queryResult = await post('/api/maintenance/database/query', { query });
          this.displayQueryResult();
        } catch (e) {
          await alert({
            title: localize({ key: 'database.query_tool.error.title' }),
            message: localize({ key: 'database.query_tool.error.message' }),
          });
        }
      });
  }
}
