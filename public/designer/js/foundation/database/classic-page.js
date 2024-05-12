import { Dexie } from '../../../lib/dexie.js';

class ClassicPageDatabase {
  constructor() {
    this.#database = new Dexie('classicPages');
    this.#database.version(1).stores({
      changes: `++id`,
    });
  }

  #database;

  getChangedPage(id) {
    return this.#database.changes.get(id);
  }

  async deleteChangedPage(id) {
    await this.#database.changes.delete(id);
  }

  async saveChangedPage(id, content) {
    const tzoffset = (new Date()).getTimezoneOffset() * 60000;
    const localISOTime = (new Date(Date.now() - tzoffset)).toISOString().slice(0, -1);


    await this.#database.changes.put({
      id,
      content,
      updated: {
        at: localISOTime,
      }
    });
  }
}

const classicPageDatabase = new ClassicPageDatabase();

export function getClassicPageDatabase() {
  return classicPageDatabase;
}
