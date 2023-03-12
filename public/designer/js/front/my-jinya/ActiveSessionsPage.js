import html from '../../../lib/jinya-html.js';
import UAParser from '../../../lib/uaparser.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import { getJinyaApiKey } from '../../foundation/storage.js';

export default class ActiveSessionsPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.sessions = [];
  }

  // eslint-disable-next-line class-methods-use-this
  getBrowser(ua) {
    const parser = new UAParser(ua);
    return localize({
      key: 'my_jinya.sessions.browser',
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
        key: 'my_jinya.sessions.device',
        values: {
          vendor: parser.getDevice().vendor,
          model: parser.getDevice().model,
        },
      });
    }

    return localize({ key: 'my_jinya.sessions.unknown_device' });
  }

  // eslint-disable-next-line class-methods-use-this
  locateIp(ip) {
    return get(`/api/ip-location/${ip}`);
  }

  displaySessions() {
    const table = document.querySelector('.cosmo-table tbody');
    clearChildren({ parent: table });
    for (const session of this.sessions) {
      const tr = document.createElement('tr');
      tr.innerHTML = html`
          <tr>
              <td>${session.validSince.toLocaleString()}</td>
              <td>${session.browser}</td>
              <td>${session.device}</td>
              <td>${session.location}</td>
              <td>${session.remoteAddress}</td>
              <td>
                  <button ${session.key === getJinyaApiKey() ? 'disabled' : ''} class="cosmo-button"
                          data-action="logout" data-apikey="${session.key}">
                      ${localize({ key: 'my_jinya.sessions.action.logout' })}
                  </button>
              </td>
          </tr>`;
      table.append(tr);
    }
    document.querySelectorAll('[data-action="logout"]').forEach((button) => {
      button.addEventListener('click', async () => {
        const key = button.getAttribute('data-apikey');
        await httpDelete(`/api/api_key/${key}`);
        const idx = this.sessions.findIndex((s) => s.key === key);
        this.sessions.splice(idx, 1);
        this.displaySessions();
      });
    });
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
        <table class="cosmo-table">
            <thead>
            <tr>
                <th>${localize({ key: 'my_jinya.sessions.table.valid_since' })}</th>
                <th>${localize({ key: 'my_jinya.sessions.table.browser' })}</th>
                <th>${localize({ key: 'my_jinya.sessions.table.device' })}</th>
                <th>${localize({ key: 'my_jinya.sessions.table.place' })}</th>
                <th>${localize({ key: 'my_jinya.sessions.table.ip' })}</th>
                <th>${localize({ key: 'my_jinya.sessions.table.actions' })}</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>`;
  }

  async displayed() {
    await super.displayed();
    const result = await get('/api/api_key');
    this.sessions = result.items.map((item) => ({
      ...item,
      validSince: new Date(Date.parse(item.validSince)),
      browser: this.getBrowser(item.userAgent),
      device: this.getDevice(item.userAgent),
    }));
    const promises = [];
    for (const session of this.sessions) {
      promises.push((async () => {
        const location = await this.locateIp(session.remoteAddress);
        if (location?.city) {
          session.location = `${location.city} ${location.region} ${location.country}`;
        } else {
          session.location = localize({ key: 'my_jinya.sessions.unknown' });
        }
      })());
    }

    await Promise.all(promises);
    this.displaySessions();
  }
}
