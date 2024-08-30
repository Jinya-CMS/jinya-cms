import { Dexie } from '../../../lib/dexie.js';

function getCookieByName(name) {
  if (!document.cookie.includes('=')) {
    return null;
  }

  return document.cookie
    .split(';')
    .map((v) => v.split('='))
    .reduce((acc, v) => {
      acc[decodeURIComponent(v[0].trim())] = decodeURIComponent(v[1].trim());

      return acc;
    }, {})[name];
}

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
        if (localStorage.getItem('/jinya/device/code')) {
          document.cookie = `JinyaDeviceCode=${localStorage.getItem('/jinya/device/code')}`;
        }
      }
    });
    this.#database.on('ready', async (db) => {
      this.#cachedApiKey = (await db.values.get(this.#apiKeyName))?.value ?? null;
    });

    this.getApiKey = this.getApiKey.bind(this);
    this.setApiKey = this.setApiKey.bind(this);
    this.deleteApiKey = this.deleteApiKey.bind(this);
  }

  #database;
  #apiKeyName = 'api-key';

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
    this.markApiKeyValid();
  }

  async deleteApiKey() {
    await this.#database.values.delete(this.#apiKeyName);
    this.#cachedApiKey = null;
    this.markApiKeyInvalid();
  }

  getDeviceCode() {
    return getCookieByName('JinyaDeviceCode');
  }

  markApiKeyValid() {
    if (localStorage) {
      localStorage.setItem('/jinya/api-key/valid', JSON.stringify(true));
    }
  }

  markApiKeyInvalid() {
    if (localStorage) {
      localStorage.removeItem('/jinya/api-key/valid');
    }
  }

  isApiKeyValid() {
    if (localStorage) {
      return localStorage.getItem('/jinya/api-key/valid') === 'true';
    }

    return false;
  }
}

const authenticationDatabase = new AuthenticationDatabase();

export function getAuthenticationDatabase() {
  return authenticationDatabase;
}
