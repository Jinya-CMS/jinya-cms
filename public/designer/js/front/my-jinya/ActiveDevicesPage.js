import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';

export default class ActiveDevicesPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.devices = [];
  }

  // eslint-disable-next-line class-methods-use-this
  getBrowser(ua) {
    const parser = new UAParser(ua);
    return localize({
      key: 'my_jinya.devices.browser',
      values: {
        browser: parser.getBrowser().name,
        os: parser.getOS().name,
      },
    });
  }

  // eslint-disable-next-line class-methods-use-this
  getDevice(ua) {
    const parser = new UAParser(ua);
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
  }

  // eslint-disable-next-line class-methods-use-this
  locateIp(ip) {
    return get(`/api/ip-location/${ip}`);
  }

  displayDevices() {
    const table = document.querySelector('.cosmo-table tbody');
    clearChildren({ parent: table });
    for (const device of this.devices) {
      const tr = document.createElement('tr');
      tr.innerHTML = html`
          <tr>
              <td>${device.browser}</td>
              <td>${device.device}</td>
              <td>${device.location}</td>
              <td>${device.remoteAddress}</td>
              <td>
                  <button class="cosmo-button" data-action="logout" data-key="${device.key}">
                      ${localize({ key: 'my_jinya.devices.action.forget' })}
                  </button>
              </td>
          </tr>`;
      table.append(tr);
    }
    document.querySelectorAll('[data-action="logout"]').forEach((button) => {
      button.addEventListener('click', async () => {
        const key = button.getAttribute('data-key');
        await httpDelete(`/api/known_device/${key}`);
        const idx = this.devices.findIndex((s) => s.key === key);
        this.devices.splice(idx, 1);
        this.displayDevices();
      });
    });
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
        <table class="cosmo-table">
            <thead>
            <tr>
                <th>${localize({ key: 'my_jinya.devices.table.browser' })}</th>
                <th>${localize({ key: 'my_jinya.devices.table.device' })}</th>
                <th>${localize({ key: 'my_jinya.devices.table.place' })}</th>
                <th>${localize({ key: 'my_jinya.devices.table.ip' })}</th>
                <th>${localize({ key: 'my_jinya.devices.table.actions' })}</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>`;
  }

  async displayed() {
    await super.displayed();
    const result = await get('/api/known_device');
    this.devices = result.items.map((item) => ({
      ...item,
      browser: this.getBrowser(item.userAgent),
      device: this.getDevice(item.userAgent),
    }));
    const promises = [];
    for (const session of this.devices) {
      promises.push((async () => {
        const location = await this.locateIp(session.remoteAddress);
        if (location?.city) {
          session.location = `${location.city} ${location.region} ${location.country}`;
        } else {
          session.location = localize({ key: 'my_jinya.devices.unknown' });
        }
      })());
    }

    await Promise.all(promises);
    this.displayDevices();
  }
}
