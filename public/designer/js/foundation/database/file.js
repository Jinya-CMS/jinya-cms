import { Dexie, liveQuery } from '../../../lib/dexie.js';

const STATE_PENDING = 'pending';
const STATE_UPLOADING = 'uploading';
const STATE_UPLOADED = 'uploaded';

const STATUS_UPLOADED = 'uploaded';
const STATUS_UPLOADING = 'uploading';
const STATUS_UPLOAD_ERROR = 'error';
const STATUS_CURRENT_UPLOAD = 'current';

class FileDatabase {
  constructor() {
    this.#database = new Dexie('files');
    this.#database.version(1).stores({
      files: '++id',
      tags: '++id',
      uploadQueue: `++id,state`,
      uploadStatus: 'key,value',
    });

    this.#database.on('ready', async (db) => {
      await db.uploadQueue.where({ state: STATE_UPLOADED }).delete();
      await db.uploadQueue.where({ state: STATE_UPLOADING }).delete();
      await db.uploadStatus.put({
        key: STATUS_UPLOADED,
        value: 0,
      });
      await db.uploadStatus.put({
        key: STATUS_UPLOADING,
        value: await db.uploadQueue.where({ state: STATE_PENDING }).count(),
      });
      await db.uploadStatus.put({
        key: STATUS_UPLOAD_ERROR,
        value: {},
      });
      await db.uploadStatus.put({
        key: STATUS_CURRENT_UPLOAD,
        value: '',
      });
    });

    this.saveFile = this.saveFile.bind(this);
    this.getAllFiles = this.getAllFiles.bind(this);
    this.getFileById = this.getFileById.bind(this);
    this.clearFiles = this.clearFiles.bind(this);
    this.deleteFile = this.deleteFile.bind(this);
    this.saveFile = this.saveFile.bind(this);
    this.saveFiles = this.saveFiles.bind(this);
    this.watchFiles = this.watchFiles.bind(this);
    this.replaceFiles = this.replaceFiles.bind(this);

    this.saveTag = this.saveTag.bind(this);
    this.getAllTags = this.getAllTags.bind(this);
    this.clearTags = this.clearTags.bind(this);
    this.deleteTag = this.deleteTag.bind(this);
    this.saveTag = this.saveTag.bind(this);
    this.saveTags = this.saveTags.bind(this);
    this.watchTags = this.watchTags.bind(this);
    this.replaceTags = this.replaceTags.bind(this);

    this.watchUploadingFilesCount = this.watchUploadingFilesCount.bind(this);
    this.watchUploadedFilesCount = this.watchUploadedFilesCount.bind(this);
    this.watchCurrentUpload = this.watchCurrentUpload.bind(this);
    this.watchPendingUploads = this.watchPendingUploads.bind(this);
    this.watchUploadError = this.watchUploadError.bind(this);
  }

  #database;

  async #openIfClosed() {
    if (!this.#database.isOpen()) {
      this.#database.open();
    }
  }

  async getAllFiles() {
    await this.#openIfClosed();
    if (this.#database.files.count() === 0) {
      return [];
    }

    return await this.#database.files.toArray();
  }

  async getFileById(id) {
    await this.#openIfClosed();

    return await this.#database.files.get(id);
  }

  async clearFiles() {
    await this.#openIfClosed();

    await this.#database.files.clear();
  }

  async deleteFile(id) {
    await this.#openIfClosed();

    await this.#database.files.delete(id);
  }

  async saveFile(file) {
    await this.#openIfClosed();

    await this.#database.files.put(file, file.id);
  }

  async saveFiles(files) {
    await this.#openIfClosed();

    await this.#database.files.bulkPut(files);
  }

  async replaceFiles(files) {
    await this.#openIfClosed();

    await this.#database.transaction('rw', this.#database.files, async () => {
      await this.#database.files.clear();
      await this.#database.files.bulkPut(files);
    });
  }

  /**
   * @returns {Observable}
   */
  watchFiles() {
    return liveQuery(() => this.#database.files.toArray());
  }

  async getAllTags() {
    await this.#openIfClosed();
    if (this.#database.tags.count() === 0) {
      return [];
    }

    return await this.#database.tags.toArray();
  }

  async clearTags() {
    await this.#openIfClosed();

    await this.#database.tags.clear();
  }

  async deleteTag(id) {
    await this.#openIfClosed();

    await this.#database.tags.delete(id);
  }

  async saveTag(tag) {
    await this.#openIfClosed();

    await this.#database.tags.put(tag);
  }

  async saveTags(tags) {
    await this.#openIfClosed();

    await this.#database.tags.bulkPut(tags);
  }

  async replaceTags(tags) {
    await this.#openIfClosed();

    await this.#database.transaction('rw', this.#database.tags, async () => {
      await this.#database.tags.clear();
      await this.#database.tags.bulkPut(tags);
    });
  }

  /**
   * @returns {Observable}
   */
  watchTags() {
    return liveQuery(() => this.#database.tags.toArray());
  }

  async queueFilesForUpload(files) {
    await this.#openIfClosed();
    const { value: uploading } = await this.#database.uploadStatus.get(STATUS_UPLOADING);
    await this.#database.uploadStatus.put({
      key: STATUS_UPLOADING,
      value: uploading + files.length,
    });

    await this.#database.uploadQueue.bulkAdd(
      files.map((file) => ({
        ...file,
        state: STATE_PENDING,
      })),
    );
  }

  async markFileUploaded(id) {
    await this.#openIfClosed();
    const { value: uploaded } = await this.#database.uploadStatus.get(STATUS_UPLOADED);
    await this.#database.uploadStatus.put({
      key: STATUS_UPLOADED,
      value: uploaded + 1,
    });

    await this.#database.uploadQueue.update(id, { state: STATE_UPLOADED });
  }

  async markFileUploading(id, name) {
    await this.#openIfClosed();

    await this.#database.uploadQueue.update(id, { state: STATE_UPLOADING });
    await this.#database.uploadStatus.update(STATUS_CURRENT_UPLOAD, { value: name });
  }

  async setUploadError(error, name) {
    await this.#openIfClosed();

    await this.#database.uploadStatus.put({
      key: STATUS_UPLOAD_ERROR,
      value: {
        error,
        name,
      },
    });
  }

  /**
   * @returns {Observable}
   */
  watchPendingUploads() {
    return liveQuery(() => this.#database.uploadQueue.where({ state: STATE_PENDING }).toArray());
  }

  watchUploadedFilesCount() {
    return liveQuery(async () => await this.#database.uploadStatus.get(STATUS_UPLOADED));
  }

  watchUploadingFilesCount() {
    return liveQuery(async () => await this.#database.uploadStatus.get(STATUS_UPLOADING));
  }

  watchUploadError() {
    return liveQuery(async () => await this.#database.uploadStatus.get(STATUS_UPLOAD_ERROR));
  }

  watchCurrentUpload() {
    return liveQuery(async () => await this.#database.uploadStatus.get(STATUS_CURRENT_UPLOAD));
  }
}

const fileDatabase = new FileDatabase();

export function getFileDatabase() {
  return fileDatabase;
}
