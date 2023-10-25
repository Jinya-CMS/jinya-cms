import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';

export default class TablesPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.databaseInfo = null;
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html` <div class="cosmo-list">
      <nav class="cosmo-list__items" id="table-list"></nav>
      <div class="cosmo-list__content">
        <div class="cosmo-tab-control">
          <div class="cosmo-tab-control__tabs">
            <a data-tab="details" class="cosmo-tab-control__tab-link cosmo-tab-control__tab-link--active"
              >${localize({ key: 'database.details' })}</a
            >
            <a data-tab="structure" class="cosmo-tab-control__tab-link">${localize({ key: 'database.structure' })}</a>
            <a data-tab="constraints" class="cosmo-tab-control__tab-link"
              >${localize({ key: 'database.constraints.title' })}</a
            >
            <a data-tab="indexes" class="cosmo-tab-control__tab-link">${localize({ key: 'database.indexes.title' })}</a>
          </div>
          <div class="cosmo-tab-control__content" id="details"></div>
          <div class="cosmo-tab-control__content" id="structure" hidden></div>
          <div class="cosmo-tab-control__content" id="constraints" hidden></div>
          <div class="cosmo-tab-control__content" id="indexes" hidden></div>
        </div>
      </div>
    </div>`;
  }

  displayTableList() {
    let list = '';
    for (const tableName of Object.keys(this.databaseInfo.tables)) {
      list += `<a class="cosmo-list__item" data-name="${tableName}">${tableName}</a>`;
    }
    clearChildren({ parent: document.getElementById('table-list') });
    document.getElementById('table-list').innerHTML = list;
    document.querySelectorAll('.cosmo-list__item').forEach((item) =>
      item.addEventListener('click', (e) => {
        e.preventDefault();
        this.selectedTable = e.currentTarget.getAttribute('data-name');
        this.displaySelectedTable();
      }),
    );
  }

  displaySelectedTable() {
    document
      .querySelectorAll('.cosmo-list__item--active')
      .forEach((item) => item.classList.remove('cosmo-list__item--active'));
    document.querySelector(`[data-name="${this.selectedTable}"]`).classList.add('cosmo-list__item--active');

    const table = this.databaseInfo.tables[this.selectedTable];
    const details = document.getElementById('details');
    const structure = document.getElementById('structure');
    const constraints = document.getElementById('constraints');
    const indexes = document.getElementById('indexes');

    details.innerHTML = html` <span class="cosmo-title">${localize({ key: 'database.details' })}</span>
      <dl class="cosmo-key-value-list">
        <dt class="cosmo-key-value-list__key">${localize({ key: 'database.entryCount' })}</dt>
        <dd class="cosmo-key-value-list__value">${table.entryCount}</dd>
        <dt class="cosmo-key-value-list__key">${localize({ key: 'database.engine' })}</dt>
        <dd class="cosmo-key-value-list__value">${table.engine}</dd>
        <dt class="cosmo-key-value-list__key">${localize({ key: 'database.size' })}</dt>
        <dd class="cosmo-key-value-list__value">${table.size / 1024} KB</dd>
      </dl>`;
    structure.innerHTML = html` <span class="cosmo-title">${localize({ key: 'database.structure' })}</span>
      <table class="cosmo-table">
        <thead>
          <tr>
            <th>${localize({ key: 'database.tables.field' })}</th>
            <th>${localize({ key: 'database.tables.type' })}</th>
            <th>${localize({ key: 'database.tables.null' })}</th>
            <th>${localize({ key: 'database.tables.key' })}</th>
            <th>${localize({ key: 'database.tables.default' })}</th>
            <th>${localize({ key: 'database.tables.extra' })}</th>
          </tr>
        </thead>
        <tbody>
          ${table.structure.map(
            (row) =>
              html` <tr>
                <td>${row.Field}</td>
                <td>${row.Type}</td>
                <td>${row.Null}</td>
                <td>${row.Key}</td>
                <td>${row.Default}</td>
                <td>${row.Extra}</td>
              </tr>`,
          )}
        </tbody>
      </table>`;
    constraints.innerHTML = html` <span class="cosmo-title">${localize({ key: 'database.constraints.title' })}</span>
      <table class="cosmo-table">
        <thead>
          <tr>
            <th>${localize({ key: 'database.constraints.constraintName' })}</th>
            <th>${localize({ key: 'database.constraints.constraintType' })}</th>
            <th>${localize({ key: 'database.constraints.columnName' })}</th>
            <th>${localize({ key: 'database.constraints.referencedTableName' })}</th>
            <th>${localize({ key: 'database.constraints.referencedColumnName' })}</th>
            <th>${localize({ key: 'database.constraints.positionInUniqueConstraint' })}</th>
            <th>${localize({ key: 'database.constraints.deleteRule' })}</th>
            <th>${localize({ key: 'database.constraints.updateRule' })}</th>
          </tr>
        </thead>
        <tbody>
          ${table.constraints.map(
            (row) =>
              html` <tr>
                <td>${row.CONSTRAINT_NAME}</td>
                <td>${row.CONSTRAINT_TYPE}</td>
                <td>${row.COLUMN_NAME}</td>
                <td>${row.REFERENCED_TABLE_NAME ?? localize({ key: 'database.constraints.none' })}</td>
                <td>${row.REFERENCED_COLUMN_NAME ?? localize({ key: 'database.constraints.none' })}</td>
                <td>${row.POSITION_IN_UNIQUE_CONSTRAINT ?? localize({ key: 'database.constraints.none' })}</td>
                <td>${row.DELETE_RULE ?? localize({ key: 'database.constraints.none' })}</td>
                <td>${row.UPDATE_RULE ?? localize({ key: 'database.constraints.none' })}</td>
              </tr>`,
          )}
        </tbody>
      </table>`;
    indexes.innerHTML = html` <span class="cosmo-title">${localize({ key: 'database.structure' })}</span>
      <table class="cosmo-table">
        <thead>
          <tr>
            <th>${localize({ key: 'database.indexes.keyName' })}</th>
            <th>${localize({ key: 'database.indexes.columnName' })}</th>
            <th>${localize({ key: 'database.indexes.cardinality' })}</th>
            <th>${localize({ key: 'database.indexes.indexType' })}</th>
            <th>${localize({ key: 'database.indexes.collation' })}</th>
            <th>${localize({ key: 'database.indexes.unique' })}</th>
          </tr>
        </thead>
        <tbody>
          ${table.indexes.map(
            (row) =>
              html` <tr>
                <td>${row.Key_name}</td>
                <td>${row.Column_name}</td>
                <td>${row.Cardinality}</td>
                <td>${row.Index_type}</td>
                <td>${row.Collation}</td>
                <td>${row.Non_unique === 0}</td>
              </tr>`,
          )}
        </tbody>
      </table>`;
  }

  async displayed() {
    await super.displayed();
    this.databaseInfo = await get('/api/maintenance/database/analyze');
    this.displayTableList();
    [this.selectedTable] = Object.keys(this.databaseInfo.tables);
    this.displaySelectedTable();
  }

  bindEvents() {
    super.bindEvents();
    document.querySelectorAll('.cosmo-tab-control__tab-link').forEach((tab) =>
      tab.addEventListener('click', () => {
        document.querySelectorAll('.cosmo-tab-control__content').forEach((tabContent) => {
          // eslint-disable-next-line no-param-reassign
          tabContent.hidden = true;
        });
        document
          .querySelectorAll('.cosmo-tab-control__tab-link--active')
          .forEach((tabLink) => tabLink.classList.remove('cosmo-tab-control__tab-link--active'));
        document.getElementById(tab.getAttribute('data-tab')).hidden = false;
        tab.classList.add('cosmo-tab-control__tab-link--active');
      }),
    );
  }
}
