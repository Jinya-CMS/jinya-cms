import html from '../../../../lib/jinya-html.js';
import { upload } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';

export default class UpdateThemeDialog {
  constructor({ onHide, id }) {
    this.id = id;
    this.onHide = onHide;
  }

  show() {
    const container = document.createElement('div');
    container.innerHTML = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="update-dialog">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">
                    ${localize({ key: 'design.themes.update.title' })}
                </h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="updateThemeZip" class="cosmo-label">
                            ${localize({ key: 'design.themes.update.file' })}
                        </label>
                        <input accept="application/zip" class="cosmo-input" required type="file" id="updateThemeZip">
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button class="cosmo-button" id="cancel-update">
                        ${localize({ key: 'design.themes.update.cancel' })}
                    </button>
                    <button type="submit" class="cosmo-button" id="save-update">
                        ${localize({ key: 'design.themes.update.save' })}
                    </button>
                </div>
            </div>
        </form>`;
    document.body.append(container);

    document.getElementById('cancel-update').addEventListener('click', () => container.remove());
    document.getElementById('update-dialog').addEventListener('submit', async (e) => {
      e.preventDefault();
      const { files } = document.getElementById('updateThemeZip');
      const [zip] = files;

      await upload(`/api/theme/${this.id}`, zip);
      container.remove();
      this.onHide();
    });
  }
}
