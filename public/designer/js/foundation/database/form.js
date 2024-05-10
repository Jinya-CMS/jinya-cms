import { Dexie } from '../../../lib/dexie.js';

class FormDatabase {
  constructor() {
    this.#database = new Dexie('forms');
    this.#database.version(1)
      .stores({
        items: `++id,formId`,
      });
  }

  #database;

  getItems(id) {
    return this.#database.items.where('formId')
      .equals(id)
      .toArray();
  }

  async deleteItems(id) {
    await this.#database.transaction('rw', this.#database.items, async () => {
      await this.#database.items.where('formId')
        .equals(id)
        .delete();
    });
  }

  async saveItems(id, items) {
    await this.#database.transaction('rw', this.#database.items, async () => {
      await this.#database.items.where('formId')
        .equals(id)
        .delete();
      await this.#database.items.bulkAdd(items);
    });
  }
}

const formDatabase = new FormDatabase();

export function getFormDatabase() {
  return formDatabase;
}
