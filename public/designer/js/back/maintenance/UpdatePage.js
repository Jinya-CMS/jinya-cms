import html from '../../../lib/jinya-html.js';
import { get, post } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import confirm from '../../foundation/ui/confirm.js';

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
      contentView.innerHTML = html` <div class="jinya-update__text">
          ${localize({
            key: 'maintenance.update.version_text',
            values: {
              ...this.version,
              openB: '<b>',
              closeB: '</b>',
            },
          })}
        </div>
        <button id="update-button" class="cosmo-button">${localize({ key: 'maintenance.update.update_now' })}</button>`;
      document.getElementById('update-button').addEventListener('click', async () => {
        if (
          await confirm({
            title: localize({ key: 'maintenance.update.perform_update.title' }),
            message: localize({ key: 'maintenance.update.perform_update.message' }),
            declineLabel: localize({ key: 'maintenance.update.perform_update.decline' }),
            approveLabel: localize({ key: 'maintenance.update.perform_update.approve' }),
          })
        ) {
          const loadingContainer = document.createElement('div');
          loadingContainer.innerHTML = html`
              <div class="cosmo-modal__backdrop"></div>
              <div class="cosmo-modal__container">
                  <div class="cosmo-modal">
                      <h1 class="cosmo-modal__title">
                          ${localize({ key: 'maintenance.update.perform_update.updating' })}</h1>
                      <p class="cosmo-modal__content">
                      <div class="jinya-loader__container" style="min-height: 300px">
                          <div class="jinya-loader"></div>
                      </div>
                      </p>
                  </div>
              </div>`;
          document.body.append(loadingContainer);
          try {
            await post('/api/update');
            // eslint-disable-next-line no-empty
          } catch (e) {}
          window.location.reload();
          loadingContainer.remove();
        }
      });
    } else {
      contentView.innerHTML = html` <div class="jinya-update__text">
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
