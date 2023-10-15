import html from '../../../../lib/jinya-html.js';
import {
 get, post, put, upload,
} from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class UploadDialog {
  /**
   * Shows the upload dialog
   * @param onHide {function({id: number, name: string, path: string})}
   */
  constructor({ onHide }) {
    this.onHide = onHide;
  }

  show() {
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="upload-dialog-form">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'media.files.upload_single_file.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="uploadFileName" class="cosmo-label">
                            ${localize({ key: 'media.files.upload_single_file.name' })}
                        </label>
                        <input required type="text" id="uploadFileName" class="cosmo-input">
                        <label for="uploadFileName" class="cosmo-label">
                            ${localize({ key: 'media.files.upload_single_file.file' })}
                        </label>
                        <input required class="cosmo-input" type="file" id="uploadFileFile">
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button class="cosmo-button" id="cancel-upload-dialog">
                        ${localize({ key: 'media.files.upload_single_file.cancel' })}
                    </button>
                    <button type="submit" class="cosmo-button" id="save-upload-dialog">
                        ${localize({ key: 'media.files.upload_single_file.upload' })}
                    </button>
                </div>
            </div>
        </form>`;

    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);

    document.getElementById('uploadFileFile').addEventListener('change', (e) => {
      const nameInput = document.getElementById('uploadFileName');
      if (nameInput.value === '') {
        const file = e.currentTarget.files[0];
        nameInput.value = file.name
          .split('.')
          .reverse()
          .slice(1)
          .reverse()
          .join('.');
      }
    });
    document.getElementById('cancel-upload-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('upload-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('uploadFileName').value;
      const { files } = document.getElementById('uploadFileFile');
      try {
        const createdFile = await post('/api/media/file', { name });
        await put(`/api/media/file/${createdFile.id}/content`);
        await upload(`/api/media/file/${createdFile.id}/content/0`, files[0]);
        await put(`/api/media/file/${createdFile.id}/content/finish`);
        const saved = await get(`/api/media/file/${createdFile.id}`);
        await this.onHide(saved);
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'media.files.upload_single_file.error.title' }),
            message: localize({ key: 'media.files.upload_single_file.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'media.files.upload_single_file.error.title' }),
            message: localize({ key: 'media.files.upload_single_file.error.generic' }),
          });
        }
      }
    });
  }
}
