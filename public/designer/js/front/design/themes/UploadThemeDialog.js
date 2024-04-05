import html from '../../../../lib/jinya-html.js';
import { uploadPost } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';

export default class UploadThemeDialog {
  constructor({ onHide }) {
    this.onHide = onHide;
  }

  show() {
    const container = document.createElement('div');
    container.innerHTML = html` <form class="cosmo-modal__container" id="update-dialog">
      <div class="cosmo-modal">
        <h1 class="cosmo-modal__title">${localize({ key: 'design.themes.create.title' })}</h1>
        <div class="cosmo-modal__content">
          <div class="cosmo-input__group">
            <label for="createThemeName" class="cosmo-label"> ${localize({ key: 'design.themes.create.name' })} </label>
            <input class="cosmo-input" required type="text" id="createThemeName" />
            <label for="createThemeZip" class="cosmo-label"> ${localize({ key: 'design.themes.create.file' })} </label>
            <input accept="application/zip" class="cosmo-input" required type="file" id="createThemeZip" />
          </div>
        </div>
        <div class="cosmo-modal__button-bar">
          <button class="cosmo-button" id="cancel-update">${localize({ key: 'design.themes.create.cancel' })}</button>
          <button type="submit" class="cosmo-button" id="save-update">
            ${localize({ key: 'design.themes.create.save' })}
          </button>
        </div>
      </div>
    </form>`;
    document.body.append(container);

    document.getElementById('cancel-update').addEventListener('click', () => container.remove());
    document.getElementById('update-dialog').addEventListener('submit', async (e) => {
      e.preventDefault();
      const { files } = document.getElementById('createThemeZip');
      const [zip] = files;
      const name = document.getElementById('createThemeName').value;

      await uploadPost(`/api/theme?name=${name}`, zip);
      container.remove();
      this.onHide({ name });
    });
  }
}
