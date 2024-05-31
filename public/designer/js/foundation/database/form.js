import { Dexie } from '../../../lib/dexie.js';
import { getTimestamp } from './utils.js';

class FormDatabase {
  constructor() {
    this.#database = new Dexie('forms');
    this.#database.version(2).stores({
      items: `++id,formId`,
      changedForms: 'formId',
    });
  }

  #database;

  getItems(id) {
    return this.#database.items.where('formId').equals(id).toArray();
  }

  getChangedForm(id) {
    return this.#database.changedForms.get(id);
  }

  async deleteItems(id) {
    await this.#database.transaction('rw', this.#database.items, async () => {
      await this.#database.items.where('formId').equals(id).delete();
    });
  }

  async saveItems(id, items) {
    await this.#database.changedForms.put({
      formId: id,
      updated: { at: getTimestamp() },
    });
    await this.#database.transaction('rw', this.#database.items, async () => {
      await this.#database.items.where('formId').equals(id).delete();
      await this.#database.items.bulkAdd(items);
    });
  }
}

const formDatabase = new FormDatabase();

export function getFormDatabase() {
  return formDatabase;
}
