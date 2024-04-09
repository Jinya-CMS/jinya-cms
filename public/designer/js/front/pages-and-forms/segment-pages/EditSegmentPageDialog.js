import html from '../../../../lib/jinya-html.js';
import { put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class EditSegmentPageDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({id: number, title: string})}
   * @param name {string}
   * @param id {number}
   */
  constructor({
                onHide,
                name,
                id,
              }) {
    this.onHide = onHide;
    this.name = name;
    this.id = id;
  }

  show() {
    const content = html` <form class="cosmo-modal__container" id="edit-dialog-form">
      <div class="cosmo-modal">
        <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.segment.edit.title' })}</h1>
        <div class="cosmo-modal__content">
          <div class="cosmo-input__group">
            <label for="editPageName" class="cosmo-label">
              ${localize({ key: 'pages_and_forms.segment.edit.name' })}
            </label>
            <input required type="text" id="editPageName" class="cosmo-input" value="${this.name}" />
          </div>
        </div>
        <div class="cosmo-modal__button-bar">
          <button type="button" class="cosmo-button" id="cancel-edit-dialog">
            ${localize({ key: 'pages_and_forms.segment.edit.cancel' })}
          </button>
          <button type="submit" class="cosmo-button" id="save-edit-dialog">
            ${localize({ key: 'pages_and_forms.segment.edit.update' })}
          </button>
        </div>
      </div>
    </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('cancel-edit-dialog')
      .addEventListener('click', () => {
        container.remove();
      });
    document.getElementById('edit-dialog-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('editPageName').value;
        try {
          await put(`/api/segment-page/${this.id}`, {
            name,
          });
          this.onHide({
            name,
            id: this.id,
          });
          container.remove();
        } catch (err) {
          if (err.status === 409) {
            await alert({
              title: localize({ key: 'pages_and_forms.segment.create.error.title' }),
              message: localize({ key: 'pages_and_forms.segment.create.error.conflict' }),
              negative: true,
            });
          } else {
            await alert({
              title: localize({ key: 'pages_and_forms.segment.create.error.title' }),
              message: localize({ key: 'pages_and_forms.segment.create.error.generic' }),
              negative: true,
            });
          }
        }
      });
  }
}
