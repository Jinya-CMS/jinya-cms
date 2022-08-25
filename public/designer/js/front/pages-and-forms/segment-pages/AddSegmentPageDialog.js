import html from '../../../../lib/jinya-html.js';
import { post } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class AddSegmentPageDialog {
  /**
   * Shows the add dialog
   * @param onHide {function({id: number, name: string})}
   */
  constructor({ onHide }) {
    this.onHide = onHide;
  }

  show() {
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="create-dialog-form">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.segment.create.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="createPageName" class="cosmo-label">
                            ${localize({ key: 'pages_and_forms.segment.create.name' })}
                        </label>
                        <input required type="text" id="createPageName" class="cosmo-input">
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button type="button" class="cosmo-button" id="cancel-add-dialog">
                        ${localize({ key: 'pages_and_forms.segment.create.cancel' })}
                    </button>
                    <button type="submit" class="cosmo-button" id="save-add-dialog">
                        ${localize({ key: 'pages_and_forms.segment.create.create' })}
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
      const name = document.getElementById('createPageName').value;
      try {
        const saved = await post('/api/segment-page', {
          name,
        });
        this.onHide(saved);
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'pages_and_forms.segment.create.error.title' }),
            message: localize({ key: 'pages_and_forms.segment.create.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'pages_and_forms.segment.create.error.title' }),
            message: localize({ key: 'pages_and_forms.segment.create.error.generic' }),
          });
        }
      }
    });
  }
}
