import html from '../../../../lib/jinya-html.js';
import { get, post } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class AddMenuDialog {
  /**
   * Shows the add dialog
   * @param onHide {function({id: number, title: string, content: string})}
   */
  constructor({ onHide }) {
    this.onHide = onHide;
  }

  async show() {
    const { items: files } = await get('/api/media/file');
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="create-dialog-form">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'design.menus.create.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="createMenuName" class="cosmo-label">
                            ${localize({ key: 'design.menus.create.name' })}
                        </label>
                        <input required type="text" id="createMenuName" class="cosmo-input">
                        <label for="createMenuLogo" class="cosmo-label">
                            ${localize({ key: 'design.menus.create.logo' })}
                        </label>
                        <select required type="text" id="createMenuLogo" class="cosmo-select">
                            <option value="-1">${localize({ key: 'design.menus.create.logo_none' })}</option>
                            ${files.map((item) => html`
                                <option value="${item.id}">${item.name}</option>`)}
                        </select>
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button type="button" class="cosmo-button" id="cancel-add-dialog">
                        ${localize({ key: 'design.menus.create.cancel' })}
                    </button>
                    <button type="submit" class="cosmo-button" id="save-add-dialog">
                        ${localize({ key: 'design.menus.create.create' })}
                    </button>
                </div>
            </div>
        </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('cancel-add-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('create-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('createMenuName').value;
      const logo = parseInt(document.getElementById('createMenuLogo').value, 10);
      try {
        const data = {
          name,
        };
        if (logo !== -1) {
          data.logo = logo;
        }
        const saved = await post('/api/menu', data);
        this.onHide(saved);
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'design.menus.create.error.title' }),
            message: localize({ key: 'design.menus.create.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'design.menus.create.error.title' }),
            message: localize({ key: 'design.menus.create.error.generic' }),
          });
        }
      }
    });
  }
}
