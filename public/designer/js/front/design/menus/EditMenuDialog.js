import html from '../../../../lib/jinya-html.js';
import { get, put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class EditMenuDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({id: number, title: string, content: string})}
   * @param id {number}
   * @param name {string}
   * @param logo {number|undefined}
   */
  constructor({
                onHide, id, name, logo,
              }) {
    this.onHide = onHide;
    this.id = id;
    this.name = name;
    this.logo = logo;
  }

  async show() {
    const { items: files } = await get('/api/media/file');
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="edit-dialog-menu">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'design.menus.edit.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="editMenuName" class="cosmo-label">
                            ${localize({ key: 'design.menus.edit.name' })}
                        </label>
                        <input value="${this.name}" required type="text" id="editMenuName" class="cosmo-input">
                        <label for="editMenuLogo" class="cosmo-label">
                            ${localize({ key: 'design.menus.edit.logo' })}
                        </label>
                        <select required type="text" id="editMenuLogo" class="cosmo-select">
                            <option ${this.logo ? '' : 'selected'} value="-1">
                                ${localize({ key: 'design.menus.edit.logo_none' })}
                            </option>
                            ${files.map((item) => html`
                                <option ${this.logo === item.id ? 'selected' : ''} value="${item.id}">${item.name}
                                </option>`)}
                        </select>
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button type="button" class="cosmo-button" id="cancel-edit-dialog">
                        ${localize({ key: 'design.menus.edit.cancel' })}
                    </button>
                    <button type="submit" class="cosmo-button" id="save-edit-dialog">
                        ${localize({ key: 'design.menus.edit.update' })}
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
    document.getElementById('edit-dialog-menu').addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('editMenuName').value;
      const logo = parseInt(document.getElementById('editMenuLogo').value, 10);
      try {
        const data = {
          name,
        };
        if (logo !== -1) {
          data.logo = logo;
        } else {
          data.logo = null;
        }
        await put(`/api/menu/${this.id}`, data);
        this.onHide({ ...data, id: this.id });
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'design.menus.edit.error.title' }),
            message: localize({ key: 'design.menus.edit.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'design.menus.edit.error.title' }),
            message: localize({ key: 'design.menus.edit.error.generic' }),
          });
        }
      }
    });
  }
}
