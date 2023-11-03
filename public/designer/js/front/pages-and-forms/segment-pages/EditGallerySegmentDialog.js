import html from '../../../../lib/jinya-html.js';
import { get, post, put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class EditGallerySegmentDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({gallery: any, position: number, segment: any})}
   * @param id {number}
   * @param galleryId {number}
   * @param position {number}
   * @param newSegment {boolean}
   */
  constructor({ onHide, id, galleryId, position, newSegment = false }) {
    this.onHide = onHide;
    this.position = position;
    this.newSegment = newSegment;
    this.id = id;
    this.galleryId = galleryId;
  }

  async show() {
    const { items } = await get('/api/gallery');
    const content = html` <div class="cosmo-modal__backdrop"></div>
      <form class="cosmo-modal__container" id="edit-dialog-form">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.segment.designer.edit.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="editSegmentGallery" class="cosmo-label">
                ${localize({ key: 'pages_and_forms.segment.designer.edit.gallery' })}
              </label>
              <select required type="text" id="editSegmentGallery" class="cosmo-select">
                ${items.map(
                  (item) =>
                    html` <option ${this.galleryId === item.id ? 'selected' : ''} value="${item.id}">
                      ${item.name}
                    </option>`,
                )}
              </select>
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button type="button" class="cosmo-button" id="cancel-edit-dialog">
              ${localize({ key: 'pages_and_forms.segment.designer.edit.cancel' })}
            </button>
            <button type="submit" class="cosmo-button" id="save-edit-dialog">
              ${localize({ key: 'pages_and_forms.segment.designer.edit.update' })}
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
      const galleryId = parseInt(document.getElementById('editSegmentGallery').value, 10);
      try {
        if (this.newSegment) {
          const segment = await post(`/api/segment-page/${this.id}/segment/gallery`, {
            gallery: galleryId,
            position: this.position,
          });
          this.onHide({ segment });
        } else {
          await put(`/api/segment-page/${this.id}/segment/${this.position}`, {
            gallery: galleryId,
          });
          this.onHide({
            position: this.position,
            gallery: items.find((g) => g.id === galleryId),
          });
        }
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
