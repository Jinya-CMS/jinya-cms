import html from '../../../lib/jinya-html.js';
import { get, put } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';

export default class UpdatePage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.version = null;
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return '<div id="content"></div>';
  }

  displayPage() {
    const contentView = document.getElementById('content');
    if (this.version.currentVersion !== this.version.mostRecentVersion) {
      contentView.innerHTML = html`
          <div class="jinya-update__text">
              ${localize({
                  key: 'maintenance.update.version_text',
                  values: {
                      ...this.version,
                      openB: '<b>',
                      closeB: '</b>',
                  },
              })}
          </div>
          <button id="update-button" class="cosmo-button">
              ${localize({ key: 'maintenance.update.update_now' })}
          </button>`;
      document.getElementById('update-button').addEventListener('click', async () => {
        await put('/api/update');
        window.location.href = '/update';
      });
    } else {
      contentView.innerHTML = html`
          <div class="jinya-update__text">
              ${localize({
                  key: 'maintenance.update.version_text_no_update',
                  values: {
                      ...this.version,
                      openB: '<b>',
                      closeB: '</b>',
                  },
              })}
          </div>`;
    }
  }

  async displayed() {
    await super.displayed();
    this.version = await get('/api/version');
    this.displayPage();
  }

  bindEvents() {
    super.bindEvents();
  }
}
