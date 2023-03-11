import html from '../../../../lib/jinya-html.js';
import { post } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class AddGalleryDialog {
  /**
   * Shows the add dialog
   * @param onHide {function({id: number, name: string, orientation: string, type: string, description: string})}
   */
  constructor({ onHide }) {
    this.onHide = onHide;
  }

  show() {
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="create-dialog-form">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'media.galleries.create.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="createGalleryName" class="cosmo-label">
                            ${localize({ key: 'media.galleries.create.name' })}
                        </label>
                        <input required type="text" id="createGalleryName" class="cosmo-input">
                        <span class="cosmo-label cosmo-label--radio">
                            ${localize({ key: 'media.galleries.create.orientation' })}
                        </span>
                        <div class="cosmo-radio__group">
                            <input name="orientation" class="cosmo-radio" type="radio" checked
                                   id="createGalleryOrientationHorizontal" value="horizontal">
                            <label for="createGalleryOrientationHorizontal">
                                ${localize({ key: 'media.galleries.create.horizontal' })}
                            </label>
                            <input name="orientation" class="cosmo-radio" type="radio"
                                   id="createGalleryOrientationVertical" value="vertical">
                            <label for="createGalleryOrientationVertical">
                                ${localize({ key: 'media.galleries.create.vertical' })}
                            </label>
                        </div>
                        <span class="cosmo-label cosmo-label--radio">
                            ${localize({ key: 'media.galleries.create.type' })}
                        </span>
                        <div class="cosmo-radio__group">
                            <input name="type" class="cosmo-radio" type="radio" id="createGalleryTypeMasonry" checked
                                   value="masonry">
                            <label for="createGalleryTypeMasonry">
                                ${localize({ key: 'media.galleries.create.masonry' })}
                            </label>
                            <input name="type" class="cosmo-radio" type="radio" id="createGalleryTypeSequence"
                                   value="sequence">
                            <label for="createGalleryTypeSequence">
                                ${localize({ key: 'media.galleries.create.sequence' })}
                            </label>
                        </div>
                        <label for="createGalleryDescription" class="cosmo-label cosmo-label--textarea">
                            ${localize({ key: 'media.galleries.create.description' })}
                        </label>
                        <textarea rows="5" id="createGalleryDescription" class="cosmo-textarea"></textarea>
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button type="button" class="cosmo-button" id="cancel-add-dialog">
                        ${localize({ key: 'media.galleries.create.cancel' })}
                    </button>
                    <button type="submit" class="cosmo-button" id="save-add-dialog">
                        ${localize({ key: 'media.galleries.create.create' })}
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
      const name = document.getElementById('createGalleryName').value;
      const description = document.getElementById('createGalleryDescription').value;
      const orientation = document.querySelector('[name="orientation"]:checked').value;
      const type = document.querySelector('[name="type"]:checked').value;
      try {
        const saved = await post('/api/media/gallery', {
          name,
          description,
          orientation,
          type,
        });
        this.onHide(saved);
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'media.galleries.edit.error.title' }),
            message: localize({ key: 'media.galleries.edit.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'media.galleries.edit.error.title' }),
            message: localize({ key: 'media.galleries.edit.error.generic' }),
          });
        }
      }
    });
  }
}
