import html from '../../../../lib/jinya-html.js';
import { put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import getEditor from '../../../foundation/ui/tiny.js';

export default class AddFormDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({id: number, title: string, toAddress: string, description: string})}
   * @param id {number}
   * @param title {string}
   * @param toAddress {string}
   * @param description {string}
   */
  constructor({ onHide, id, title, toAddress, description }) {
    this.onHide = onHide;
    this.id = id;
    this.title = title;
    this.toAddress = toAddress;
    this.description = description;
  }

  async show() {
    const content = html` <form class="cosmo-modal__container" id="edit-dialog-form">
      <div class="cosmo-modal">
        <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.form.edit.title' })}</h1>
        <div class="cosmo-modal__content">
          <div class="cosmo-input__group">
            <label for="editFormTitle" class="cosmo-label">
              ${localize({ key: 'pages_and_forms.form.edit.form_title' })}
            </label>
            <input required type="text" id="editFormTitle" class="cosmo-input" value="${this.title}" />
            <label for="editToAddress" class="cosmo-label">
              ${localize({ key: 'pages_and_forms.form.edit.to_address' })}
            </label>
            <input required type="email" id="editFormToAddress" class="cosmo-input" value="${this.toAddress}" />
            <label for="editDescription" class="cosmo-label is--textarea">
              ${localize({ key: 'pages_and_forms.form.edit.description' })}
            </label>
            <textarea id="editDescription" hidden></textarea>
          </div>
        </div>
        <div class="cosmo-modal__button-bar">
          <button type="button" class="cosmo-button" id="cancel-edit-dialog">
            ${localize({ key: 'pages_and_forms.form.edit.cancel' })}
          </button>
          <button type="submit" class="cosmo-button" id="save-edit-dialog">
            ${localize({ key: 'pages_and_forms.form.edit.update' })}
          </button>
        </div>
      </div>
    </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    const tiny = await getEditor({ element: document.getElementById('editDescription') });
    tiny.setContent(this.description);

    document.getElementById('cancel-edit-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('edit-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const title = document.getElementById('editFormTitle').value;
        const toAddress = document.getElementById('editFormToAddress').value;
        const description = tiny.getContent();
        await put(`/api/form/${this.id}`, {
          title,
          toAddress,
          description,
        });
        this.onHide({
          id: this.id,
          title,
          description,
          toAddress,
        });
        tinymce.remove();
        tiny.destroy();
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'pages_and_forms.form.edit.error.title' }),
            message: localize({ key: 'pages_and_forms.form.edit.error.conflict' }),
            negative: true,
          });
        } else {
          await alert({
            title: localize({ key: 'pages_and_forms.form.edit.error.title' }),
            message: localize({ key: 'pages_and_forms.form.edit.error.generic' }),
            negative: true,
          });
        }
      }
    });
  }
}
