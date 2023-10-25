import html from '../../../../lib/jinya-html.js';
import { put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class EditSimplePageDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({id: number, title: string})}
   * @param title {string}
   * @param id {number}
   */
  constructor({ onHide, title, id }) {
    this.onHide = onHide;
    this.title = title;
    this.id = id;
  }

  show() {
    const content = html` <div class="cosmo-modal__backdrop"></div>
      <form class="cosmo-modal__container" id="edit-dialog-form">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.simple.edit.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="editPageTitle" class="cosmo-label">
                ${localize({ key: 'pages_and_forms.simple.edit.page_title' })}
              </label>
              <input required type="text" id="editPageTitle" class="cosmo-input" value="${this.title}" />
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button type="button" class="cosmo-button" id="cancel-edit-dialog">
              ${localize({ key: 'pages_and_forms.simple.edit.cancel' })}
            </button>
            <button type="submit" class="cosmo-button" id="save-edit-dialog">
              ${localize({ key: 'pages_and_forms.simple.edit.update' })}
            </button>
          </div>
        </div>
      </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('cancel-edit-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('edit-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const title = document.getElementById('editPageTitle').value;
      try {
        await put(`/api/simple-page/${this.id}`, {
          title,
        });
        this.onHide({ title, id: this.id });
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
