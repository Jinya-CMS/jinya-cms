import { Dexie } from '../../../lib/dexie.js';
import { getTimestamp } from './utils.js';

class BlogPostDatabase {
  constructor() {
    this.#database = new Dexie('blogPosts');
    this.#database.version(1).stores({
      sections: `++id,postId`,
      changedPages: 'postId',
    });
  }

  #database;

  getSections(id) {
    return this.#database.sections.where('postId').equals(id).toArray();
  }

  getChangedPage(id) {
    return this.#database.changedPages.get(id);
  }

  async deleteSections(id) {
    await this.#database.transaction('rw', this.#database.sections, async () => {
      await this.#database.sections.where('postId').equals(id).delete();
    });
  }

  async saveSections(id, sections) {
    await this.#database.changedPosts.put({
      pageId: id,
      updated: { at: getTimestamp() },
    });
    await this.#database.transaction('rw', this.#database.sections, async () => {
      await this.#database.sections.where('postId').equals(id).delete();
      await this.#database.sections.bulkAdd(sections);
    });
  }
}

const blogPostDatabase = new BlogPostDatabase();

export function getBlogPostDatabase() {
  return blogPostDatabase;
}
