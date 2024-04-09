import { Alpine } from '../../../../lib/alpine.js';
import { deleteApiKey, getApiKeys, locateIp } from '../../foundation/api/authentication.js';
import { getJinyaApiKey } from '../../foundation/storage.js';
import UAParser from '../../../lib/uaparser.js';
import localize from '../../foundation/localize.js';

Alpine.data('sessionsData', () => ({
  sessions: [],
  async init() {
    const apiKeys = await getApiKeys();
    this.sessions = apiKeys.items;
    this.currentApiKey = getJinyaApiKey();
  },
  getBrowser(userAgent) {
    const parser = new UAParser(userAgent);
    return localize({
      key: 'my_jinya.sessions.browser',
      values: {
        browser: parser.getBrowser().name,
        os: parser.getOS().name,
      },
    });
  },
  getDevice(userAgent) {
    const parser = new UAParser(userAgent);
    if (parser.getDevice().vendor) {
      return localize({
        key: 'my_jinya.sessions.device',
        values: {
          vendor: parser.getDevice().vendor,
          model: parser.getDevice().model,
        },
      });
    }

    return localize({ key: 'my_jinya.sessions.unknown_device' });
  },
  getValidSince(validSince) {
    const date = new Date(Date.parse(validSince.toLocaleString()));

    return date.toLocaleString();
  },
  async getLocation(ip) {
    const location = await locateIp(ip);
    if (location?.city) {
      return `${location.city} ${location.region} ${location.country}`;
    } else {
      return localize({ key: 'my_jinya.sessions.unknown' });
    }
  },
  async logout(key) {
    await deleteApiKey(key);
    this.sessions = this.sessions.filter((k) => k.key !== key);
  },
}));
