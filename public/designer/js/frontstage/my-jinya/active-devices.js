import { Alpine } from '../../../../lib/alpine.js';
import { deleteKnownDevice, getKnownDevices, locateIp } from '../../foundation/api/authentication.js';
import { getJinyaApiKey } from '../../foundation/storage.js';
import UAParser from '../../../lib/uaparser.js';
import localize from '../../foundation/localize.js';

Alpine.data('devicesData', () => ({
  devices: [],
  async init() {
    const devices = await getKnownDevices();
    this.devices = devices.items;
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
        key: 'my_jinya.devices.device',
        values: {
          vendor: parser.getDevice().vendor,
          model: parser.getDevice().model,
        },
      });
    }

    return localize({ key: 'my_jinya.devices.unknown_device' });
  },
  getValidSince(validSince) {
    const date = new Date(Date.parse(validSince.toLocaleString()));

    return date.toLocaleString();
  },
  async getLocation(ip) {
    const location = await locateIp(ip);
    if (location?.city) {
      return `${location.city} ${location.region} ${location.country}`;
    }

    return localize({ key: 'my_jinya.devices.unknown' });
  },
  async forget(key) {
    await deleteKnownDevice(key);
    this.devices = this.devices.filter((k) => k.key !== key);
  },
}));
