import html from '../../../../lib/jinya-html.js';
import { post } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class AddSimplePageDialog {
  /**
   * Shows the add dialog
   * @param onHide {function({id: number, title: string, content: string})}
   */
  constructor({ onHide }) {
    this.onHide = onHide;
  }

  show() {
    const content = html` <div class="cosmo-modal__backdrop"></div>
      <form class="cosmo-modal__container" id="create-dialog-form">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.simple.create.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="createPageTitle" class="cosmo-label">
                ${localize({ key: 'pages_and_forms.simple.create.page_title' })}
              </label>
              <input required type="text" id="createPageTitle" class="cosmo-input" />
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button type="button" class="cosmo-button" id="cancel-add-dialog">
              ${localize({ key: 'pages_and_forms.simple.create.cancel' })}
            </button>
            <button type="submit" class="cosmo-button" id="save-add-dialog">
              ${localize({ key: 'pages_and_forms.simple.create.create' })}
            </button>
          </div>
        </div>
      </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('cancel-add-dialog')
      .addEventListener('click', () => {
        container.remove();
      });
    document.getElementById('create-dialog-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const title = document.getElementById('createPageTitle').value;
        try {
          const saved = await post('/api/simple-page', {
            title,
            content: '',
          });
          this.onHide(saved);
          container.remove();
        } catch (err) {
          if (err.status === 409) {
            await alert({
              title: localize({ key: 'pages_and_forms.simple.create.error.title' }),
              message: localize({ key: 'pages_and_forms.simple.create.error.conflict' }),
            });
          } else {
            await alert({
              title: localize({ key: 'pages_and_forms.simple.create.error.title' }),
              message: localize({ key: 'pages_and_forms.simple.create.error.generic' }),
            });
          }
        }
      });
  }
}
