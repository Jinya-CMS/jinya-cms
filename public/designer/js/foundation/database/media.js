import { Dexie, liveQuery } from '../../../lib/dexie.js';
import { getRootFolder } from '../api/media.js';

class MediaDatabase {
  constructor() {
    this.#database = new Dexie('media');
    this.#database.version(1).stores({
      files: '++id,name,folderId',
      folders: '++id,name,parentId',
      tags: '++id,name',
    });

    this.replaceMedia = this.replaceMedia.bind(this);
  }

  #database;

  async #openIfClosed() {
    if (!this.#database.isOpen()) {
      this.#database.open();
    }
  }

  async cacheMedia() {
    const media = await getRootFolder();
    await this.replaceMedia(media);
  }

  #flattenFolders(folders, parentId = -1) {
    const f = [];
    for (const folder of folders) {
      f.push(
        {
          id: folder.id,
          name: folder.name,
          files: folder.files,
          parentId,
        },
        ...this.#flattenFolders(folder.folders, folder.id),
      );
    }

    return f;
  }

  async replaceMedia(media) {
    const tags = media.tags;
    const files = media.files.map((file) => ({
      ...file,
      folderId: -1,
    }));
    const folders = this.#flattenFolders(media.folders);
    files.push(
      ...folders.flatMap((folder) =>
        folder.files.map((file) => ({
          ...file,
          folderId: folder.id,
        })),
      ),
    );

    await this.#openIfClosed();

    await this.#database.transaction('rw', this.#database.files, async () => {
      await this.#database.files.clear();
      await this.#database.files.bulkPut(files);
    });

    await this.#database.transaction('rw', this.#database.folders, async () => {
      await this.#database.folders.clear();
      await this.#database.folders.bulkPut(
        folders.map((folder) => ({
          id: folder.id,
          name: folder.name,
          parentId: folder.parentId,
        })),
      );
    });

    await this.#database.transaction('rw', this.#database.tags, async () => {
      await this.#database.tags.clear();
      await this.#database.tags.bulkPut(tags);
    });
  }

  /**
   * @param parentId
   * @returns {Observable}
   */
  watchFolders(parentId = null) {
    if (parentId !== null) {
      return liveQuery(() => this.#database.folders.where('parentId').equals(parentId).sortBy('name'));
    }

    return liveQuery(() => this.#database.folders.toArray());
  }

  /**
   * @param folderId
   * @returns {Observable}
   */
  watchFiles(folderId) {
    return liveQuery(() => this.#database.files.where('folderId').equals(folderId).sortBy('name'));
  }

  async getAllTags() {
    await this.#openIfClosed();
    if (this.#database.tags.count() === 0) {
      return [];
    }

    return await this.#database.tags.toArray();
  }

  async deleteTag(id) {
    await this.#openIfClosed();

    await this.#database.tags.delete(id);
  }

  async saveTag(tag) {
    await this.#openIfClosed();

    await this.#database.tags.put(tag);
  }

  /**
   * @returns {Observable}
   */
  watchTags() {
    return liveQuery(() => this.#database.tags.orderBy('name').toArray());
  }
}

const mediaDatabase = new MediaDatabase();

export function getMediaDatabase() {
  return mediaDatabase;
}
