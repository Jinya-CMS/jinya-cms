import { Dexie } from '../../../lib/dexie.js';

class MenuDatabase {
  constructor() {
    this.#database = new Dexie('menus');
    this.#database.version(1).stores({
      items: `++id,menuId`,
    });
  }

  #database;

  getItems(id) {
    return this.#database.items.where('menuId').equals(id).toArray();
  }

  async deleteItems(id) {
    await this.#database.transaction('rw', this.#database.items, async () => {
      await this.#database.items.where('menuId').equals(id).delete();
    });
  }

  async saveItems(id, items) {
    await this.#database.transaction('rw', this.#database.items, async () => {
      await this.#database.items.where('menuId').equals(id).delete();
      await this.#database.items.bulkAdd(items);
    });
  }
}

const menuDatabase = new MenuDatabase();

export function getMenuDatabase() {
  return menuDatabase;
}
