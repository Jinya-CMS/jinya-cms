import html from '../../../lib/jinya-html.js';
import Sortable from '../../../lib/sortable.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete, put } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';

export default class FormPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.forms = [];
    this.selectedForm = {};
    this.selectedFormItem = {};
    this.formItems = [];
    this.resultSortable = null;
    this.toolboxSortable = null;
  }

  // eslint-disable-next-line class-methods-use-this
  resetPositions() {
    document
      .getElementById('item-list')
      .querySelectorAll('[data-position]')
      .forEach((elem, key) => {
        elem.setAttribute('data-position', key.toString(10));
      });
  }

  selectForm({ id }) {
    this.selectedForm = this.forms.find((p) => p.id === parseInt(id, 10));
    document
      .querySelectorAll('.cosmo-list__item--active')
      .forEach((item) => item.classList.remove('cosmo-list__item--active'));
    document.querySelector(`[data-id="${id}"]`).classList.add('cosmo-list__item--active');
    document.getElementById('edit-item').setAttribute('disabled', 'disabled');
    document.getElementById('delete-item').setAttribute('disabled', 'disabled');
  }

  async displaySelectedForm() {
    document.getElementById('form-title').innerText = `#${this.selectedForm.id} ${this.selectedForm.title}`;
    this.formItems = await get(`/api/form/${this.selectedForm.id}/item`);
    this.displayItems();
  }

  selectItem({ itemPosition }) {
    document
      .querySelectorAll('.jinya-designer-item--selected')
      .forEach((item) => item.classList.remove('jinya-designer-item--selected'));

    const item = document.querySelector(`[data-position="${itemPosition}"]`);
    item.classList.add('jinya-designer-item--selected');

    document.getElementById('edit-item').removeAttribute('disabled');
    document.getElementById('delete-item').removeAttribute('disabled');
    this.selectedFormItem = this.formItems.find((s) => s.position === parseInt(itemPosition, 10));
  }

  displayItems() {
    if (this.resultSortable) {
      this.resultSortable.destroy();
    }
    const itemList = document.getElementById('item-list');
    clearChildren({ parent: itemList });
    for (const item of this.formItems) {
      const itemElem = document.createElement('div');
      itemElem.classList.add('jinya-designer-item', 'jinya-designer-item--html');
      itemElem.setAttribute('data-position', item.position.toString(10));
      itemElem.setAttribute('data-id', item.id.toString(10));
      itemElem.innerHTML = html` <span class="jinya-designer-item__title">
          ${localize({ key: `pages_and_forms.form.designer.type_${item.type}` })}
        </span>
        <span class="jinya-form-item__label">${item.label}</span>`;
      itemList.appendChild(itemElem);
    }
    document.querySelectorAll('#item-list .jinya-designer-item').forEach((item) => {
      item.addEventListener('click', () => {
        this.selectItem({ itemPosition: item.getAttribute('data-position') });
      });
    });
    this.resultSortable = new Sortable(document.getElementById('item-list'), {
      group: { name: 'form', put: true, pull: false },
      sort: true,
      onAdd: async (e) => {
        const dropIdx = e.newIndex;
        let createPosition = 0;
        if (this.formItems.length === 0) {
          createPosition = 0;
        } else if (this.formItems.length === dropIdx) {
          createPosition = this.formItems[this.formItems.length - 1].position + 2;
        } else {
          createPosition = this.formItems[dropIdx].position;
        }
        const createNewItemType = e.item.getAttribute('data-type');
        await this.openItemEditorCreate({ position: createPosition, type: createNewItemType });
      },
      onUpdate: async (e) => {
        const oldPosition = e.item.getAttribute('data-position');
        const position = e.newIndex;
        this.resetPositions();
        await put(`/api/form/${this.selectedForm.id}/item/${oldPosition}`, {
          newPosition: position > oldPosition ? position + 1 : position,
        });
      },
    });
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
      <div class="cosmo-list">
        <nav class="cosmo-list__items" id="form-list"></nav>
        <div class="cosmo-list__content jinya-designer">
          <div class="jinya-designer__title">
            <span class="cosmo-title" id="form-title"></span>
          </div>
          <div class="cosmo-toolbar cosmo-toolbar--designer">
            <div class="cosmo-toolbar__group">
              <button id="edit-form" class="cosmo-button">
                ${localize({ key: 'pages_and_forms.form.action.edit' })}
              </button>
              <button id="delete-form" class="cosmo-button">
                ${localize({ key: 'pages_and_forms.form.action.delete' })}
              </button>
            </div>
            <div class="cosmo-toolbar__group">
              <button id="edit-item" class="cosmo-button" disabled>
                ${localize({ key: 'pages_and_forms.form.action.edit_item' })}
              </button>
              <button id="delete-item" class="cosmo-button" disabled>
                ${localize({ key: 'pages_and_forms.form.action.delete_item' })}
              </button>
            </div>
          </div>
          <div class="jinya-designer__content">
            <div id="item-list" class="jinya-designer__result jinya-designer__result--horizontal"></div>
            <div id="item-toolbox" class="jinya-designer__toolbox">
              <div data-type="text" class="jinya-designer-item__template">
                <span class="jinya-designer__drag-handle"></span>
                <span>${localize({ key: 'pages_and_forms.form.designer.type_text' })}</span>
              </div>
              <div data-type="email" class="jinya-designer-item__template">
                <span class="jinya-designer__drag-handle"></span>
                <span>${localize({ key: 'pages_and_forms.form.designer.type_email' })}</span>
              </div>
              <div data-type="textarea" class="jinya-designer-item__template">
                <span class="jinya-designer__drag-handle"></span>
                <span>${localize({ key: 'pages_and_forms.form.designer.type_textarea' })}</span>
              </div>
              <div data-type="select" class="jinya-designer-item__template">
                <span class="jinya-designer__drag-handle"></span>
                <span>${localize({ key: 'pages_and_forms.form.designer.type_select' })}</span>
              </div>
              <div data-type="checkbox" class="jinya-designer-item__template">
                <span class="jinya-designer__drag-handle"></span>
                <span>${localize({ key: 'pages_and_forms.form.designer.type_checkbox' })}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  displayForms() {
    let list = '';
    for (const form of this.forms) {
      list += `<a class="cosmo-list__item" data-id="${form.id}">${form.title}</a>`;
    }
    clearChildren({ parent: document.getElementById('form-list') });
    document.getElementById('form-list').innerHTML = `${list}
                <button id="new-form-button" class="cosmo-button cosmo-button--full-width">
                    ${localize({ key: 'pages_and_forms.form.action.new' })}
                </button>`;
    document.querySelectorAll('.cosmo-list__item').forEach((item) => {
      item.addEventListener('click', async () => {
        this.selectForm({ id: item.getAttribute('data-id') });
        await this.displaySelectedForm();
      });
    });
    document.getElementById('new-form-button').addEventListener('click', async () => {
      const { default: AddFormDialog } = await import('./forms/AddFormDialog.js');
      const dialog = new AddFormDialog({
        onHide: async (form) => {
          this.forms.push(form);
          this.displayForms();
          this.selectForm({ id: form.id });
          await this.displaySelectedForm();
        },
      });
      await dialog.show();
    });
  }

  async displayed() {
    await super.displayed();
    const { items } = await get('/api/form');
    this.forms = items;

    this.toolboxSortable = new Sortable(document.getElementById('item-toolbox'), {
      group: { name: 'form', put: false, pull: 'clone' },
      sort: false,
      handle: '.jinya-designer__drag-handle',
      onEnd(e) {
        if (!e.to.classList.contains('jinya-designer__toolbox')) {
          e.item.remove();
        }
      },
    });

    this.displayForms();
    if (this.forms.length > 0) {
      this.selectForm({ id: this.forms[0].id });
      await this.displaySelectedForm();
    }
  }

  async openItemEditorUpdate() {
    const { type } = this.selectedFormItem;
    if (type === 'email') {
      const { default: EditEmailItemDialog } = await import('./forms/EditEmailItemDialog.js');
      const dialog = new EditEmailItemDialog({
        onHide: ({ label, placeholder, helpText, isRequired, isFromAddress }) => {
          this.selectedFormItem.label = label;
          this.selectedFormItem.placeholder = placeholder;
          this.selectedFormItem.helpText = helpText;
          this.selectedFormItem.isRequired = isRequired;
          this.selectedFormItem.isFromAddress = isFromAddress;

          const editedItem = this.formItems.find((f) => f.id === this.selectedFormItem.id);
          editedItem.label = label;
          editedItem.placeholder = placeholder;
          editedItem.helpText = helpText;
          editedItem.isRequired = isRequired;
          editedItem.isFromAddress = isFromAddress;

          document.querySelector(
            `[data-position="${this.selectedFormItem.position}"] .jinya-form-item__label`,
          ).innerText = label;
        },
        ...this.selectedFormItem,
        formId: this.selectedForm.id,
      });
      dialog.show();
    } else if (type === 'textarea') {
      const { default: EditMultilineItemDialog } = await import('./forms/EditMultilineItemDialog.js');
      const dialog = new EditMultilineItemDialog({
        onHide: ({ label, placeholder, helpText, isRequired, spamFilter }) => {
          this.selectedFormItem.label = label;
          this.selectedFormItem.placeholder = placeholder;
          this.selectedFormItem.helpText = helpText;
          this.selectedFormItem.isRequired = isRequired;
          this.selectedFormItem.spamFilter = spamFilter;

          const editedItem = this.formItems.find((f) => f.id === this.selectedFormItem.id);
          editedItem.label = label;
          editedItem.placeholder = placeholder;
          editedItem.helpText = helpText;
          editedItem.isRequired = isRequired;
          editedItem.spamFilter = spamFilter;

          document.querySelector(
            `[data-position="${this.selectedFormItem.position}"] .jinya-form-item__label`,
          ).innerText = label;
        },
        ...this.selectedFormItem,
        formId: this.selectedForm.id,
      });
      dialog.show();
    } else if (type === 'checkbox') {
      const { default: EditCheckboxItemDialog } = await import('./forms/EditCheckboxItemDialog.js');
      const dialog = new EditCheckboxItemDialog({
        onHide: ({ label, placeholder, helpText, isRequired }) => {
          this.selectedFormItem.label = label;
          this.selectedFormItem.placeholder = placeholder;
          this.selectedFormItem.helpText = helpText;
          this.selectedFormItem.isRequired = isRequired;

          const editedItem = this.formItems.find((f) => f.id === this.selectedFormItem.id);
          editedItem.label = label;
          editedItem.placeholder = placeholder;
          editedItem.helpText = helpText;
          editedItem.isRequired = isRequired;

          document.querySelector(
            `[data-position="${this.selectedFormItem.position}"] .jinya-form-item__label`,
          ).innerText = label;
        },
        ...this.selectedFormItem,
        formId: this.selectedForm.id,
      });
      dialog.show();
    } else if (type === 'select') {
      const { default: EditDropdownItemDialog } = await import('./forms/EditDropdownItemDialog.js');
      const dialog = new EditDropdownItemDialog({
        onHide: ({ label, placeholder, helpText, isRequired, options }) => {
          this.selectedFormItem.label = label;
          this.selectedFormItem.placeholder = placeholder;
          this.selectedFormItem.helpText = helpText;
          this.selectedFormItem.isRequired = isRequired;
          this.selectedFormItem.options = options;

          const editedItem = this.formItems.find((f) => f.id === this.selectedFormItem.id);
          editedItem.label = label;
          editedItem.placeholder = placeholder;
          editedItem.helpText = helpText;
          editedItem.isRequired = isRequired;
          editedItem.options = options;

          document.querySelector(
            `[data-position="${this.selectedFormItem.position}"] .jinya-form-item__label`,
          ).innerText = label;
        },
        ...this.selectedFormItem,
        formId: this.selectedForm.id,
      });
      dialog.show();
    } else {
      const { default: EditTextItemDialog } = await import('./forms/EditTextItemDialog.js');
      const dialog = new EditTextItemDialog({
        onHide: ({ label, placeholder, helpText, isRequired, isSubject, spamFilter }) => {
          this.selectedFormItem.label = label;
          this.selectedFormItem.placeholder = placeholder;
          this.selectedFormItem.helpText = helpText;
          this.selectedFormItem.isRequired = isRequired;
          this.selectedFormItem.isSubject = isSubject;
          this.selectedFormItem.spamFilter = spamFilter;

          const editedItem = this.formItems.find((f) => f.id === this.selectedFormItem.id);
          editedItem.label = label;
          editedItem.placeholder = placeholder;
          editedItem.helpText = helpText;
          editedItem.isRequired = isRequired;
          editedItem.isSubject = isSubject;
          editedItem.spamFilter = spamFilter;

          document.querySelector(
            `[data-position="${this.selectedFormItem.position}"] .jinya-form-item__label`,
          ).innerText = label;
        },
        ...this.selectedFormItem,
        formId: this.selectedForm.id,
      });
      dialog.show();
    }
  }

  async openItemEditorCreate({ position, type }) {
    if (type === 'email') {
      const { default: EditEmailItemDialog } = await import('./forms/EditEmailItemDialog.js');
      const dialog = new EditEmailItemDialog({
        onHide: ({ item }) => {
          this.formItems.splice(position, 0, item);
          this.displayItems();
        },
        position,
        formId: this.selectedForm.id,
        newItem: true,
      });
      dialog.show();
    } else if (type === 'textarea') {
      const { default: EditMultilineItemDialog } = await import('./forms/EditMultilineItemDialog.js');
      const dialog = new EditMultilineItemDialog({
        onHide: ({ item }) => {
          this.formItems.splice(position, 0, item);
          this.displayItems();
        },
        position,
        formId: this.selectedForm.id,
        newItem: true,
      });
      dialog.show();
    } else if (type === 'checkbox') {
      const { default: EditCheckboxItemDialog } = await import('./forms/EditCheckboxItemDialog.js');
      const dialog = new EditCheckboxItemDialog({
        onHide: ({ item }) => {
          this.formItems.splice(position, 0, item);
          this.displayItems();
        },
        position,
        formId: this.selectedForm.id,
        newItem: true,
      });
      dialog.show();
    } else if (type === 'select') {
      const { default: EditDropdownItemDialog } = await import('./forms/EditDropdownItemDialog.js');
      const dialog = new EditDropdownItemDialog({
        onHide: ({ item }) => {
          this.formItems.splice(position, 0, item);
          this.displayItems();
        },
        position,
        formId: this.selectedForm.id,
        newItem: true,
      });
      dialog.show();
    } else {
      const { default: EditTextItemDialog } = await import('./forms/EditTextItemDialog.js');
      const dialog = new EditTextItemDialog({
        onHide: ({ item }) => {
          this.formItems.splice(position, 0, item);
          this.displayItems();
        },
        position,
        formId: this.selectedForm.id,
        newItem: true,
      });
      dialog.show();
    }
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('edit-form').addEventListener('click', async () => {
      const { default: EditItemPageDialog } = await import('./forms/EditFormDialog.js');
      const dialog = new EditItemPageDialog({
        onHide: async ({ id, title, description, toAddress }) => {
          const form = this.forms.find((p) => p.id === id);
          form.title = title;
          form.description = description;
          form.toAddress = toAddress;
          this.displayForms();
          this.selectForm({ id });
          await this.displaySelectedForm();
        },
        id: this.selectedForm.id,
        title: this.selectedForm.title,
        description: this.selectedForm.description,
        toAddress: this.selectedForm.toAddress,
      });
      await dialog.show();
    });
    document.getElementById('delete-form').addEventListener('click', async () => {
      const confirmation = await confirm({
        title: localize({ key: 'pages_and_forms.form.delete.title' }),
        message: localize({ key: 'pages_and_forms.form.delete.message', values: this.selectedForm }),
        approveLabel: localize({ key: 'pages_and_forms.form.delete.delete' }),
        declineLabel: localize({ key: 'pages_and_forms.form.delete.keep' }),
      });
      if (confirmation) {
        try {
          await httpDelete(`/api/form/${this.selectedForm.id}`);
          this.forms = this.forms.filter((form) => form.id !== this.selectedForm.id);
          this.displayForms();
          if (this.forms.length > 0) {
            this.selectForm({ id: this.forms[0].id });
          } else {
            this.selectedForm = null;
            document.getElementById('form-title').innerText = `#${this.selectedForm.id} ${this.selectedForm.title}`;
            this.formItems = [];
            this.displayItems();
          }
          document.getElementById('edit-item').setAttribute('disabled', 'disabled');
          document.getElementById('delete-item').setAttribute('disabled', 'disabled');
        } catch (e) {
          if (e.status === 409) {
            await alert({
              title: localize({ key: 'pages_and_forms.form.delete.error.title' }),
              message: localize({ key: 'pages_and_forms.form.delete.error.conflict' }),
            });
          } else {
            await alert({
              title: localize({ key: 'pages_and_forms.form.delete.error.title' }),
              message: localize({ key: 'pages_and_forms.form.delete.error.generic' }),
            });
          }
        }
      }
    });
    document.getElementById('edit-item').addEventListener('click', async () => {
      await this.openItemEditorUpdate();
    });
    document.getElementById('delete-item').addEventListener('click', async () => {
      const confirmation = await confirm({
        title: localize({ key: 'pages_and_forms.form.delete_item.title' }),
        message: localize({ key: 'pages_and_forms.form.delete_item.message', values: this.selectedForm }),
        approveLabel: localize({ key: 'pages_and_forms.form.delete_item.delete' }),
        declineLabel: localize({ key: 'pages_and_forms.form.delete_item.keep' }),
      });
      if (confirmation) {
        const { position } = this.selectedFormItem;
        const itemElem = document.querySelector(`[data-position="${position}"]`);
        itemElem.remove();
        this.resetPositions();
        this.selectedFormItem = null;
        document.getElementById('edit-item').setAttribute('disabled', 'disabled');
        document.getElementById('delete-item').setAttribute('disabled', 'disabled');
        await httpDelete(`/api/form/${this.selectedForm.id}/item/${position}`);
      }
    });
  }
}
