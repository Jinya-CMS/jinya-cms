import { Dexie } from '../../../lib/dexie.js';

class ModernPageDatabase {
  constructor() {
    this.#database = new Dexie('modernPages');
    this.#database.version(1).stores({
      sections: `++id,pageId`,
    });
  }

  #database;

  getSections(id) {
    return this.#database.sections.where('pageId').equals(id).toArray();
  }

  async deleteSections(id) {
    await this.#database.transaction('rw', this.#database.sections, async () => {
      await this.#database.sections.where('pageId').equals(id).delete();
    });
  }

  async saveSections(id, sections) {
    await this.#database.transaction('rw', this.#database.sections, async () => {
      await this.#database.sections.where('pageId').equals(id).delete();
      await this.#database.sections.bulkAdd(sections);
    });
  }
}

const modernPageDatabase = new ModernPageDatabase();

export function getModernPageDatabase() {
  return modernPageDatabase;
}
