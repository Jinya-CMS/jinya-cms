import { Dexie } from '../../../lib/dexie.js';
import { getTimestamp } from './utils.js';

class ModernPageDatabase {
  constructor() {
    this.#database = new Dexie('modernPages');
    this.#database.version(2).stores({
      sections: `++id,pageId`,
      changedPages: 'pageId',
    });
  }

  #database;

  getSections(id) {
    return this.#database.sections.where('pageId').equals(id).toArray();
  }

  getChangedPage(id) {
    return this.#database.changedPages.get(id);
  }

  async deleteSections(id) {
    await this.#database.transaction('rw', this.#database.sections, async () => {
      await this.#database.sections.where('pageId').equals(id).delete();
    });
  }

  async saveSections(id, sections) {
    await this.#database.changedPages.put({
      pageId: id,
      updated: { at: getTimestamp() },
    });
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
