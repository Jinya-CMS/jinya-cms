import { Dexie, liveQuery } from '../../../lib/dexie.js';

const STATE_PENDING = 'pending';
const STATE_UPLOADING = 'uploading';
const STATE_UPLOADED = 'uploaded';

const STATUS_UPLOADED = 'uploaded';
const STATUS_UPLOADING = 'uploading';
const STATUS_UPLOAD_ERROR = 'error';
const STATUS_CURRENT_UPLOAD = 'current';
const STATUS_RECENT_UPLOAD = 'recent';

class FileDatabase {
  constructor() {
    this.#database = new Dexie('files');
    this.#database.version(1).stores({
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
      await db.uploadStatus.put({
        key: STATUS_RECENT_UPLOAD,
        value: '',
      });
    });

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

  async setRecentUpload(name) {
    await this.#openIfClosed();

    await this.#database.uploadStatus.update(STATUS_RECENT_UPLOAD, { value: name });
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

  watchRecentUpload() {
    return liveQuery(async () => await this.#database.uploadStatus.get(STATUS_RECENT_UPLOAD));
  }
}

const fileDatabase = new FileDatabase();

export function getFileDatabase() {
  return fileDatabase;
}
