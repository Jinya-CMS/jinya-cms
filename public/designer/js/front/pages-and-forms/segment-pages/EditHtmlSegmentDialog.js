import html from '../../../../lib/jinya-html.js';
import { post, put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import getEditor from '../../../foundation/ui/tiny.js';

export default class EditFileSegmentDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({position: number, html: string, segment: any})}
   * @param id {number}
   * @param html {string}
   * @param position {number}
   * @param newSegment {boolean}
   */
  constructor({
                onHide,
                id,
                html: content,
                position,
                newSegment = false,
              }) {
    this.onHide = onHide;
    this.position = position;
    this.newSegment = newSegment;
    this.id = id;
    this.html = content;
  }

  async show() {
    const content = html` <div class="cosmo-modal__backdrop"></div>
      <form class="cosmo-modal__container" id="edit-dialog-form">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.segment.designer.edit.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <textarea id="editHtml" hidden></textarea>
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
    const tiny = await getEditor({ element: document.getElementById('editHtml') });
    tiny.setContent(this.html);

    document.getElementById('cancel-edit-dialog')
      .addEventListener('click', () => {
        container.remove();
      });
    document.getElementById('edit-dialog-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
          if (this.newSegment) {
            const segment = await post(`/api/segment-page/${this.id}/segment/html`, {
              position: this.position,
              html: tiny.getContent(),
            });
            this.onHide({ segment });
          } else {
            await put(`/api/segment-page/${this.id}/segment/${this.position}`, {
              html: tiny.getContent(),
            });
            this.onHide({
              position: this.position,
              html: tiny.getContent(),
            });
          }
          tinymce.remove();
          container.remove();
        } catch (err) {
          await alert({
            title: localize({ key: 'pages_and_forms.segment.designer.edit.error.title' }),
            message: localize({ key: 'pages_and_forms.segment.designer.edit.error.generic' }),
          });
        }
      });
  }
}
