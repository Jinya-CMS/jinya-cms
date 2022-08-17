import html from '../../../../lib/jinya-html.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import { get, put, upload } from '../../../foundation/http/request.js';

export default class EditDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({id: number, name: string, path: string})}
   * @param file {{id: number, name: string}}
   */
  constructor({ onHide, file }) {
    this.onHide = onHide;
    this.file = file;
  }

  show() {
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="edit-dialog-form">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'media.files.edit.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="editFileName" class="cosmo-label">
                            ${localize({ key: 'media.files.edit.name' })}
                        </label>
                        <input required type="text" id="editFileName" class="cosmo-input" value="${this.file.name}">
                        <label for="editFileName" class="cosmo-label">
                            ${localize({ key: 'media.files.edit.file' })}
                        </label>
                        <input class="cosmo-input" type="file" id="editFileFile">
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button class="cosmo-button" id="cancel-edit-dialog">
                        ${localize({ key: 'media.files.edit.cancel' })}
                    </button>
                    <button type="submit" class="cosmo-button" id="save-edit-dialog">
                        ${localize({ key: 'media.files.edit.save' })}
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
      const name = document.getElementById('editFileName').value;
      const { files } = document.getElementById('editFileFile');
      try {
        await put(`/api/media/file/${this.file.id}`, { name });
        if (files.length === 1) {
          if (files && files.length > 0) {
            await put(`/api/media/file/${this.file.id}/content`);
            await upload(`/api/media/file/${this.file.id}/content/0`, files[0]);
            await put(`/api/media/file/${this.file.id}/content/finish`);
          }
        }
        const saved = await get(`/api/media/file/${this.file.id}`);
        this.onHide(saved);
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'media.files.delete.error.title' }),
            message: localize({ key: 'media.files.delete.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'media.files.delete.error.title' }),
            message: localize({ key: 'media.files.delete.error.generic' }),
          });
        }
      }
    });
  }
}
