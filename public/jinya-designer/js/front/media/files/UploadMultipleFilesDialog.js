import html from '../../../../lib/jinya-html.js';
import localize from '../../../foundation/localize.js';
import dataUrlReader from '../../../foundation/blob/dataUrlReader.js';

export default class UploadMultipleFilesDialog {
  /**
   * Shows the upload dialog
   * @param onHide {function({files: FileList})}
   */
  constructor({ onHide }) {
    this.onHide = onHide;
  }

  show() {
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <div class="cosmo-modal__container">
            <div class="cosmo-modal jinya-modal--multiple-files">
                <h1 class="cosmo-modal__title">${localize({ key: 'media.files.upload_multiple_files.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="uploadMultipleFilesPicker" class="cosmo-label">
                            ${localize({ key: 'media.files.upload_multiple_files.files' })}
                        </label>
                        <input class="cosmo-input" multiple required type="file" id="uploadMultipleFilesPicker">
                    </div>
                    <div class="jinya-media-tile__container--modal">
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button class="cosmo-button" id="cancel-upload-dialog">
                        ${localize({ key: 'media.files.upload_multiple_files.cancel' })}
                    </button>
                    <button class="cosmo-button" id="save-upload-dialog">
                        ${localize({ key: 'media.files.upload_multiple_files.upload' })}
                    </button>
                </div>
            </div>
        </div>`;

    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);

    document.getElementById('uploadMultipleFilesPicker').addEventListener('change', async (e) => {
      const tileContainer = document.querySelector('.jinya-media-tile__container--modal');
      const selectedFiles = e.currentTarget.files;
      for (const selectedFile of selectedFiles) {
        // eslint-disable-next-line no-await-in-loop
        const src = await dataUrlReader({ file: selectedFile });
        const tile = document.createElement('div');
        tile.classList.add('jinya-media-tile', 'jinya-media-tile--small');
        tile.innerHTML = `<img class="jinya-media-tile__img jinya-media-tile__img--small" src="${src}">`;
        tileContainer.append(tile);
      }
    });
    document.getElementById('cancel-upload-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('save-upload-dialog').addEventListener('click', () => {
      this.onHide({ files: document.getElementById('uploadMultipleFilesPicker').files });
      container.remove();
    });
  }
}
