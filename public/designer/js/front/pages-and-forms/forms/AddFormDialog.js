import html from '../../../../lib/jinya-html.js';
import { post } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import getEditor from '../../../foundation/ui/tiny.js';

export default class AddFormDialog {
  /**
   * Shows the create dialog
   * @param onHide {function({id: number, title: string, toAddress: string, description: string})}
   */
  constructor({ onHide }) {
    this.onHide = onHide;
  }

  async show() {
    const content = html` <div class="cosmo-modal__backdrop"></div>
      <form class="cosmo-modal__container" id="create-dialog-form">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.form.create.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="createFormTitle" class="cosmo-label">
                ${localize({ key: 'pages_and_forms.form.create.form_title' })}
              </label>
              <input required type="text" id="createFormTitle" class="cosmo-input" />
              <label for="createToAddress" class="cosmo-label">
                ${localize({ key: 'pages_and_forms.form.create.to_address' })}
              </label>
              <input required type="email" id="createFormToAddress" class="cosmo-input" />
              <label for="createDescription" class="cosmo-label cosmo-label--textarea">
                ${localize({ key: 'pages_and_forms.form.create.description' })}
              </label>
              <textarea id="createDescription" hidden></textarea>
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button type="button" class="cosmo-button" id="cancel-create-dialog">
              ${localize({ key: 'pages_and_forms.form.create.cancel' })}
            </button>
            <button type="submit" class="cosmo-button" id="save-create-dialog">
              ${localize({ key: 'pages_and_forms.form.create.create' })}
            </button>
          </div>
        </div>
      </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    const tiny = await getEditor({ element: document.getElementById('createDescription') });
    document.getElementById('cancel-create-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('create-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const title = document.getElementById('createFormTitle').value;
        const toAddress = document.getElementById('createFormToAddress').value;
        const description = tiny.getContent();
        const saved = await post('/api/form', {
          title,
          toAddress,
          description,
        });
        this.onHide(saved);
        tinymce.remove();
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'pages_and_forms.form.create.error.title' }),
            message: localize({ key: 'pages_and_forms.form.create.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'pages_and_forms.form.create.error.title' }),
            message: localize({ key: 'pages_and_forms.form.create.error.generic' }),
          });
        }
      }
    });
  }
}
