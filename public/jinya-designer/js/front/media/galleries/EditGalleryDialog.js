import html from '../../../../lib/jinya-html.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import { put } from '../../../foundation/http/request.js';

export default class EditGalleryDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({id: number, name: string, orientation: string, type: string, description: string})}
   * @param gallery {{id: number, name: string, description: string, orientation: string, type: string}}
   */
  constructor({ onHide, gallery }) {
    this.onHide = onHide;
    this.gallery = gallery;
  }

  show() {
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="edit-dialog-form">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'media.galleries.edit.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="editGalleryName" class="cosmo-label">
                            ${localize({ key: 'media.galleries.edit.name' })}
                        </label>
                        <input value="${this.gallery.name}" required type="text" id="editGalleryName"
                               class="cosmo-input">
                        <span class="cosmo-label cosmo-label--radio">
                            ${localize({ key: 'media.galleries.edit.orientation' })}
                        </span>
                        <div class="cosmo-radio__group">
                            <input name="orientation" class="cosmo-radio" type="radio"
                                   ${this.gallery.orientation.toLowerCase() === 'horizontal' ? 'checked' : ''}
                                   id="editGalleryOrientationHorizontal" value="horizontal">
                            <label for="editGalleryOrientationHorizontal">
                                ${localize({ key: 'media.galleries.edit.horizontal' })}
                            </label>
                            <input name="orientation" class="cosmo-radio" type="radio"
                                   ${this.gallery.orientation.toLowerCase() === 'vertical' ? 'checked' : ''}
                                   id="editGalleryOrientationVertical" value="vertical">
                            <label for="editGalleryOrientationVertical">
                                ${localize({ key: 'media.galleries.edit.vertical' })}
                            </label>
                        </div>
                        <span class="cosmo-label cosmo-label--radio">
                            ${localize({ key: 'media.galleries.edit.type' })}
                        </span>
                        <div class="cosmo-radio__group">
                            <input name="type" class="cosmo-radio" type="radio" id="editGalleryTypeMasonry"
                                   ${this.gallery.type.toLowerCase() === 'masonry' ? 'checked' : ''} value="masonry">
                            <label for="editGalleryTypeMasonry">
                                ${localize({ key: 'media.galleries.edit.masonry' })}
                            </label>
                            <input name="type" class="cosmo-radio" type="radio" id="editGalleryTypeSequence"
                                   ${this.gallery.type.toLowerCase() === 'sequence' ? 'checked' : ''} value="sequence">
                            <label for="editGalleryTypeSequence">
                                ${localize({ key: 'media.galleries.edit.sequence' })}
                            </label>
                        </div>
                        <label for="editGalleryDescription" class="cosmo-label cosmo-label--textarea">
                            ${localize({ key: 'media.galleries.edit.description' })}
                        </label>
                        <textarea rows="5" id="editGalleryDescription" class="cosmo-textarea">
                            ${this.gallery.description}
                        </textarea>
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button type="button" class="cosmo-button" id="cancel-add-dialog">
                        ${localize({ key: 'media.galleries.edit.cancel' })}
                    </button>
                    <button type="submit" class="cosmo-button" id="save-add-dialog">
                        ${localize({ key: 'media.galleries.edit.update' })}
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
    document.getElementById('edit-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('editGalleryName').value;
      const description = document.getElementById('editGalleryDescription').value;
      const orientation = document.querySelector('[name="orientation"]:checked').value;
      const type = document.querySelector('[name="type"]:checked').value;
      try {
        await put(`/api/media/gallery/${this.gallery.id}`, {
          name,
          description,
          orientation,
          type,
        });
        this.onHide({
          id: this.gallery.id,
          name,
          description,
          orientation,
          type,
        });
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
