import { Alpine } from '../../../../lib/alpine.js';
import { deleteApiKey, getApiKeys, locateIp } from '../../foundation/api/authentication.js';
import UAParser from '../../../lib/uaparser.js';
import localize from '../../foundation/utils/localize.js';

Alpine.data('sessionsData', () => ({
  sessions: [],
  currentApiKey: '',
  async init() {
    const apiKeys = await getApiKeys();
    this.sessions = apiKeys.items;
  },
  getBrowser(userAgent) {
    const parser = new UAParser(userAgent);
    if (parser.getBrowser().name) {
      return localize({
        key: 'my_jinya.devices.browser',
        values: {
          browser: parser.getBrowser().name,
          os: parser.getOS().name,
        },
      });
    }

    return localize({ key: 'my_jinya.devices.unknown_browser' });
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
      return `${location.city} ${localize({ key: `countries.${location.country}` })}`;
    }

    return localize({ key: 'my_jinya.sessions.unknown' });
  },
  async logout(key) {
    await deleteApiKey(key);
    this.sessions = this.sessions.filter((k) => k.key !== key);
  },
}));
