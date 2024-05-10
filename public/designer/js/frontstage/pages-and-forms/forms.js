import { Alpine } from '../../../../lib/alpine.js';
import {
  createForm,
  deleteForm,
  getFormItems,
  getForms,
  updateForm,
  updateFormItems,
} from '../../foundation/api/forms.js';
import localize from '../../foundation/utils/localize.js';
import confirm from '../../foundation/ui/confirm.js';
import alert from '../../foundation/ui/alert.js';
import { Dexie } from '../../../lib/dexie.js';
import isEqual from '../../../lib/lodash/isEqual.js';

import '../../foundation/ui/components/toolbar-editor.js';

const dexie = new Dexie('forms');

Alpine.data('formsData', () => ({
  forms: [],
  items: [],
  selectedForm: null,
  draggingItem: null,
  draggingItemIndex: null,
  getItemTitle(type) {
    return localize({ key: `pages_and_forms.form.designer.type_${type}` });
  },
  async saveFormItems() {
    await this.clearFormItems();
    await dexie.items.bulkAdd(this.cleanItems(Alpine.raw(this.items), this.selectedForm.id));
  },
  async clearFormItems() {
    await dexie.items.where('formId')
      .equals(this.selectedForm.id)
      .delete();
  },
  async getFormItems(id) {
    return this.cleanItems(await dexie.items.where('formId')
      .equals(id)
      .toArray());
  },
  cleanItems(items, formId = null) {
    return items.map((item) => ({
      type: item.type,
      options: item.options,
      spamFilter: item.spamFilter,
      label: item.label,
      helpText: item.helpText,
      isFromAddress: item.isFromAddress,
      isSubject: item.isSubject,
      isRequired: item.isRequired,
      placeholder: item.placeholder,
      formId: formId ?? item.formId,
    }));
  },
  async init() {
    dexie.version(1)
      .stores({
        items: `++id,formId`,
      });
    if (!dexie.isOpen()) {
      dexie.open();
    }

    const forms = await getForms();
    this.forms = forms.items;
    if (this.forms.length > 0) {
      await this.selectForm(this.forms[0]);
    }
  },
  destroy() {
    if (this.create.editor) {
      this.create.editor.remove();
    }
    if (this.edit.editor) {
      this.edit.editor.remove();
    }
    dexie.close();
  },
  async selectForm(form) {
    if (form) {
      this.items = this.cleanItems(await getFormItems(form.id), form.id);
    } else {
      this.items = [];
    }

    this.selectedForm = form;
    const savedFormItems = await this.getFormItems(form.id);
    if (savedFormItems.length === 0) {
      await this.saveFormItems();
    } else if (savedFormItems.length !== this.items.length || !isEqual(savedFormItems, Alpine.raw(this.items))) {
      const confirmed = await confirm({
        title: localize({ key: 'pages_and_forms.form.designer.load.title' }),
        message: localize({ key: 'pages_and_forms.form.designer.load.message' }),
        declineLabel: localize({ key: 'pages_and_forms.form.designer.load.decline' }),
        approveLabel: localize({ key: 'pages_and_forms.form.designer.load.approve' }),
      });
      if (confirmed) {
        this.items = savedFormItems;
      } else {
        await this.saveFormItems();
      }
    }
  },
  openCreateDialog() {
    this.create.error.reset();
    this.create.toAddress = '';
    this.create.title = '';
    this.create.description = '';
    this.create.open = true;
  },
  openEditDialog() {
    this.edit.error.reset();
    this.edit.toAddress = this.selectedForm.toAddress;
    this.edit.title = this.selectedForm.title;
    this.edit.description = this.selectedForm.description;
    this.edit.open = true;
  },
  openAddItemDialog(index) {
    this.addItem.index = index;
    this.addItem.open = true;
    this.addItem.type = 'text';
    this.addItem.options = '';
    this.addItem.spamFilter = '';
    this.addItem.label = '';
    this.addItem.helpText = '';
    this.addItem.isFromAddress = false;
    this.addItem.isSubject = false;
    this.addItem.isRequired = true;
    this.addItem.placeholder = '';
  },
  openEditItemDialog(item, index) {
    this.editItem.index = index;
    this.editItem.open = true;
    this.editItem.type = item.type;
    this.editItem.options = item.options?.join('\n') ?? '';
    this.editItem.spamFilter = item.spamFilter?.join('\n') ?? '';
    this.editItem.label = item.label;
    this.editItem.helpText = item.helpText;
    this.editItem.isFromAddress = item.isFromAddress;
    this.editItem.isSubject = item.isSubject;
    this.editItem.isRequired = item.isRequired;
    this.editItem.placeholder = item.placeholder;
  },
  startItemDrag(item, index) {
    this.draggingItem = item;
    this.draggingItemIndex = index;
  },
  async dragOver(index) {
    const items = this.items;
    if (index > this.draggingItemIndex) {
      items.splice(index + 1, 0, Alpine.raw(this.draggingItem));
      items.splice(this.draggingItemIndex, 1);
    } else if (index < this.draggingItemIndex) {
      items.splice(this.draggingItemIndex, 1);
      items.splice(index, 0, Alpine.raw(this.draggingItem));
    }

    this.items = items;
    await this.saveFormItems();
    this.draggingItemIndex = index;
  },
  async createForm() {
    try {
      const form = await createForm(this.create.title, this.create.toAddress, this.create.description);
      this.forms.push(form);
      await this.selectForm(form);

      this.create.open = false;
    } catch (e) {
      if (e.status === 409) {
        this.create.error.message = localize({ key: 'pages_and_forms.form.create.error.conflict' });
      } else {
        this.create.error.message = localize({ key: 'pages_and_forms.form.create.error.generic' });
      }
      this.create.error.title = localize({ key: 'pages_and_forms.form.create.error.title' });
      this.create.error.hasError = true;
    }
  },
  async updateForm() {
    try {
      await updateForm(this.selectedForm.id, this.edit.title, this.edit.toAddress, this.edit.description);
      const formIdx = this.forms.findIndex((form) => this.selectedForm.id === form.id);

      this.forms[formIdx].title = this.edit.title;
      this.forms[formIdx].toAddress = this.edit.toAddress;
      this.forms[formIdx].description = this.edit.description;

      await this.selectForm(this.forms[formIdx]);

      this.edit.open = false;
    } catch (e) {
      if (e.status === 409) {
        this.edit.error.message = localize({ key: 'pages_and_forms.form.edit.error.conflict' });
      } else {
        this.edit.error.message = localize({ key: 'pages_and_forms.form.edit.error.generic' });
      }
      this.edit.error.title = localize({ key: 'pages_and_forms.form.edit.error.title' });
      this.edit.error.hasError = true;
    }
  },
  async deleteForm() {
    const confirmed = await confirm({
      title: localize({ key: 'pages_and_forms.form.delete.title' }),
      message: localize({
        key: 'pages_and_forms.form.delete.message',
        values: this.selectedForm,
      }),
      approveLabel: localize({ key: 'pages_and_forms.form.delete.delete' }),
      declineLabel: localize({ key: 'pages_and_forms.form.delete.keep' }),
      negative: true,
    });
    if (confirmed) {
      try {
        await deleteForm(this.selectedForm.id);
        this.forms = this.forms.filter((form) => form.id !== this.selectedForm.id);
        if (this.forms.length > 0) {
          await this.selectForm(this.forms[0]);
        } else {
          await this.selectForm(null);
        }
      } catch (e) {
        let message = '';
        if (e.status === 409) {
          message = localize({ key: 'pages_and_forms.form.delete.error.conflict' });
        } else {
          message = localize({ key: 'pages_and_forms.form.delete.error.generic' });
        }
        await alert({
          title: localize({ key: 'pages_and_forms.form.delete.error.title' }),
          message,
          negative: true,
        });
      }
    }
  },
  async deleteItem(item, index) {
    const confirmed = await confirm({
      title: localize({ key: 'pages_and_forms.form.delete_item.title' }),
      message: localize({
        key: 'pages_and_forms.form.delete_item.message',
        values: item,
      }),
      approveLabel: localize({ key: 'pages_and_forms.form.delete_item.delete' }),
      declineLabel: localize({ key: 'pages_and_forms.form.delete_item.keep' }),
      negative: true,
    });
    if (confirmed) {
      this.items.splice(index, 1);
      await this.saveFormItems();
    }
  },
  async updateItem() {
    this.items[this.editItem.index].options = this.editItem.options.split('\n');
    this.items[this.editItem.index].spamFilter = this.editItem.spamFilter.split('\n');
    this.items[this.editItem.index].label = this.editItem.label;
    this.items[this.editItem.index].helpText = this.editItem.helpText;
    this.items[this.editItem.index].isFromAddress = this.editItem.isFromAddress;
    this.items[this.editItem.index].isSubject = this.editItem.isSubject;
    this.items[this.editItem.index].isRequired = this.editItem.isRequired;
    this.items[this.editItem.index].placeholder = this.editItem.placeholder;
    this.editItem.open = false;
    await this.saveFormItems();
  },
  async insertItem() {
    const newItem = {
      options: this.addItem.options.split('\n'),
      spamFilter: this.addItem.spamFilter.split('\n'),
      label: this.addItem.label,
      helpText: this.addItem.helpText,
      isFromAddress: this.addItem.isFromAddress,
      isSubject: this.addItem.isSubject,
      isRequired: this.addItem.isRequired,
      placeholder: this.addItem.placeholder,
      type: this.addItem.type,
    };

    this.items.splice(this.addItem.index, 0, newItem);

    this.addItem.open = false;
    await this.saveFormItems();
  },
  async saveForm() {
    try {
      await updateFormItems(this.selectedForm.id, this.items);
      this.editItem.message.hasMessage = true;
      this.editItem.message.isNegative = false;
      this.editItem.message.title = localize({ key: 'pages_and_forms.form.designer.success.title' });
      this.editItem.message.content = localize({ key: 'pages_and_forms.form.designer.success.message' });
      setTimeout(() => {
        this.editItem.message.hasMessage = false;
      }, 30000);
      await this.clearFormItems();
    } catch (e) {
      this.editItem.message.hasMessage = true;
      this.editItem.message.isNegative = true;
      this.editItem.message.title = localize({ key: 'pages_and_forms.form.designer.error.title' });
      this.editItem.message.content = localize({ key: 'pages_and_forms.form.designer.error.message' });
    }
  },
  create: {
    open: false,
    title: '',
    description: '',
    toAddress: '',
    error: {
      reset() {
        this.hasError = false;
        this.title = '';
        this.message = '';
      },
      hasError: false,
      title: '',
      message: '',
    },
  },
  edit: {
    open: false,
    title: '',
    toAddress: '',
    description: '',
    error: {
      reset() {
        this.hasError = false;
        this.title = '';
        this.message = '';
      },
      hasError: false,
      title: '',
      message: '',
    },
  },
  editItem: {
    open: false,
    index: 0,
    type: '',
    options: '',
    spamFilter: '',
    label: '',
    helpText: '',
    isFromAddress: false,
    isSubject: false,
    isRequired: false,
    placeholder: '',
    message: {
      reset() {
        this.hasMessage = false;
        this.title = '';
        this.content = '';
      },
      hasMessage: false,
      isNegative: false,
      title: '',
      content: '',
    },
  },
  addItem: {
    open: false,
    index: 0,
    type: '',
    options: '',
    spamFilter: '',
    label: '',
    helpText: '',
    isFromAddress: false,
    isSubject: false,
    isRequired: false,
    placeholder: '',
    message: {
      reset() {
        this.hasMessage = false;
        this.title = '';
        this.content = '';
      },
      hasMessage: false,
      isNegative: false,
      title: '',
      content: '',
    },
  },
}));
