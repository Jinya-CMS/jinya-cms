import html from '../../../../lib/jinya-html.js';
import { get, post, put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import filePicker from '../../../foundation/ui/filePicker.js';

export default class EditFileSegmentDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({file: any, position: number, target: string, segment: any})}
   * @param id {number}
   * @param fileId {number}
   * @param target {string}
   * @param position {number}
   * @param newSegment {boolean}
   */
  constructor({
                onHide,
                id,
                fileId,
                position,
                target,
                newSegment = false,
              }) {
    this.onHide = onHide;
    this.position = position;
    this.newSegment = newSegment;
    this.id = id;
    this.fileId = fileId;
    this.target = target;
  }

  async show() {
    const { items } = await get('/api/file');
    const file = items.find((f) => f.id === this.fileId);
    const content = html`
      <form class="cosmo-modal__container" id="edit-dialog-form">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.segment.designer.edit.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="editSegmentFile" class="cosmo-label">
                ${localize({ key: 'pages_and_forms.segment.designer.edit.file' })}
              </label>
              <button class="cosmo-input is--picker" id="editSegmentFilePicker"
                      data-picker="${localize({ key: 'pages_and_forms.segment.designer.edit.file_picker_label' })}"
                      type="button">
                ${file ? file.name : localize({ key: 'pages_and_forms.segment.designer.edit.please_select' })}
              </button>
              <input type="hidden" id="editSegmentFile" value="${file?.id}" />
              <img alt="" id="selectedFile" class="jinya-picker__selected-file" src="${file?.path}"
                   ${file ? '' : 'hidden'} />
              <div class="cosmo-input__group is--checkbox">
                <input class="cosmo-checkbox" type="checkbox" id="editSegmentLink" ${this.target ? 'checked' : ''} />
                <label for="editSegmentLink">
                  ${localize({ key: 'pages_and_forms.segment.designer.edit.has_link' })}
                </label>
              </div>
              <label for="editSegmentTarget" class="cosmo-label" ${!this.target ? 'hidden' : ''}>
                ${localize({ key: 'pages_and_forms.segment.designer.edit.target' })}
              </label>
              <input type="text" id="editSegmentTarget" class="cosmo-input" value="${this.target ?? ''}"
                     ${!this.target ? 'hidden' : ''} />
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
    document.getElementById('editSegmentFilePicker')
      .addEventListener('click', async (e) => {
        e.preventDefault();
        const selectedFileId = parseInt(document.getElementById('editSegmentFile').value, 10);
        const fileResult = await filePicker({
          title: localize({ key: 'pages_and_forms.segment.designer.edit.file' }),
          selectedFileId,
        });
        if (fileResult) {
          document.getElementById('selectedFile').src = fileResult.path;
          document.getElementById('selectedFile').alt = fileResult.name;
          document.getElementById('selectedFile').hidden = false;

          document.getElementById('editSegmentFile').value = fileResult.id;
          document.getElementById('editSegmentFilePicker').innerText = fileResult.name;
        }
      });
    document.getElementById('cancel-edit-dialog')
      .addEventListener('click', () => {
        container.remove();
      });
    document.getElementById('editSegmentLink')
      .addEventListener('change', () => {
        const hasLink = document.getElementById('editSegmentLink').checked;
        document.getElementById('editSegmentTarget').hidden = !hasLink;
        document.querySelector('[for="editSegmentTarget"]').hidden = !hasLink;
      });
    document.getElementById('edit-dialog-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const fileId = parseInt(document.getElementById('editSegmentFile').value, 10);
        try {
          const action = {
            action: 'none',
          };
          const target = document.getElementById('editSegmentTarget').value;
          if (document.getElementById('editSegmentLink').checked && target) {
            action.action = 'link';
            action.target = target;
          }
          if (this.newSegment) {
            const segment = await post(`/api/segment-page/${this.id}/segment/file`, {
              file: fileId,
              position: this.position,
              ...action,
            });
            this.onHide({ segment });
          } else {
            await put(`/api/segment-page/${this.id}/segment/${this.position}`, {
              file: fileId,
              ...action,
            });
            this.onHide({
              position: this.position,
              file: items.find((g) => g.id === fileId),
              target: action.target,
            });
          }
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
