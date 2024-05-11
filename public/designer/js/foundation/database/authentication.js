import { Dexie } from '../../../lib/dexie.js';

class AuthenticationDatabase {
  constructor() {
    this.#database = new Dexie('authentication');
    this.#database.version(1).stores({
      values: '',
    });

    this.#database.on('populate', (tx) => {
      if (localStorage) {
        tx.values.add(
          {
            value: localStorage.getItem('/jinya/api/key'),
          },
          this.#apiKeyName,
        );
        tx.values.add(
          {
            value: localStorage.getItem('/jinya/device/code'),
          },
          this.#deviceCodeName,
        );
      }
    });
    this.#database.on('ready', async (db) => {
      this.#cachedDeviceCode = (await db.values.get(this.#deviceCodeName))?.value ?? null;
      this.#cachedApiKey = (await db.values.get(this.#apiKeyName))?.value ?? null;
    });

    this.getApiKey = this.getApiKey.bind(this);
    this.setApiKey = this.setApiKey.bind(this);
    this.deleteApiKey = this.deleteApiKey.bind(this);

    this.getDeviceCode = this.getDeviceCode.bind(this);
    this.setDeviceCode = this.setDeviceCode.bind(this);
    this.deleteDeviceCode = this.deleteDeviceCode.bind(this);
  }

  #database;
  #apiKeyName = 'api-key';
  #deviceCodeName = 'device-code';

  #cachedApiKey = null;
  #cachedDeviceCode = null;

  async getApiKey() {
    if (this.#cachedApiKey) {
      return this.#cachedApiKey;
    }

    this.#cachedApiKey = (await this.#database.values.get(this.#apiKeyName))?.value ?? null;

    return this.#cachedApiKey;
  }

  async setApiKey(value) {
    await this.#database.values.put(
      {
        key: this.#apiKeyName,
        value,
      },
      this.#apiKeyName,
    );
    this.#cachedApiKey = value;
  }

  async deleteApiKey() {
    await this.#database.values.delete(this.#apiKeyName);
    this.#cachedApiKey = null;
  }

  async getDeviceCode() {
    if (this.#cachedDeviceCode) {
      return this.#cachedDeviceCode;
    }

    this.#cachedDeviceCode = (await this.#database.values.get(this.#deviceCodeName))?.value ?? null;

    return this.#cachedDeviceCode;
  }

  async setDeviceCode(value) {
    await this.#database.values.put(
      {
        key: this.#deviceCodeName,
        value,
      },
      this.#deviceCodeName,
    );
    this.#cachedDeviceCode = value;
  }

  async deleteDeviceCode() {
    await this.#database.values.delete(this.#deviceCodeName);
    this.#cachedDeviceCode = null;
  }
}

const authenticationDatabase = new AuthenticationDatabase();

export function getAuthenticationDatabase() {
  return authenticationDatabase;
}
