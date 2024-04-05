import html from '../../../../lib/jinya-html.js';
import { get } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import filePicker from '../../../foundation/ui/filePicker.js';

export default class EditFileSegmentDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({file: any, position: number, link: string, segment: any})}
   * @param fileId {number}
   * @param link {string}
   * @param position {number}
   */
  constructor({ onHide, fileId, position, link }) {
    this.onHide = onHide;
    this.position = position;
    this.fileId = fileId;
    this.link = link;
  }

  async show() {
    const { items: files } = await get('/api/file');
    const file = files.find((f) => f.id === this.fileId);
    const content = html` <form class="cosmo-modal__container" id="edit-dialog-form">
      <div class="cosmo-modal">
        <h1 class="cosmo-modal__title">${localize({ key: 'blog.posts.designer.edit.title' })}</h1>
        <div class="cosmo-modal__content">
          <div class="cosmo-input__group">
            <label for="editSegmentFile" class="cosmo-label">
              ${localize({ key: 'blog.posts.designer.edit.file' })}
            </label>
            <button
              class="cosmo-input is--picker"
              id="editSegmentFilePicker"
              data-picker="${localize({ key: 'blog.posts.designer.edit.file_picker_label' })}"
              type="button"
            >
              ${file ? file.name : localize({ key: 'blog.posts.designer.edit.please_select' })}
            </button>
            <input type="hidden" id="editSegmentFile" value="${file?.id}" />
            <img
              alt=""
              id="selectedFile"
              class="jinya-picker__selected-file"
              src="${file?.path}"
              ${file ? '' : 'hidden'}
            />
            <div class="cosmo-input__group is--checkbox">
              <input class="cosmo-checkbox" type="checkbox" id="editSegmentHasLink" ${this.link ? 'checked' : ''} />
              <label for="editSegmentHasLink"> ${localize({ key: 'blog.posts.designer.edit.has_link' })} </label>
            </div>
            <label for="editSegmentLink" class="cosmo-label" ${!this.link ? 'hidden' : ''}>
              ${localize({ key: 'blog.posts.designer.edit.link' })}
            </label>
            <input
              type="text"
              id="editSegmentLink"
              class="cosmo-input"
              value="${this.link ?? ''}"
              ${!this.link ? 'hidden' : ''}
            />
          </div>
        </div>
        <div class="cosmo-modal__button-bar">
          <button type="button" class="cosmo-button" id="cancel-edit-dialog">
            ${localize({ key: 'blog.posts.designer.edit.cancel' })}
          </button>
          <button type="submit" class="cosmo-button" id="save-edit-dialog">
            ${localize({ key: 'blog.posts.designer.edit.update' })}
          </button>
        </div>
      </div>
    </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('editSegmentFilePicker').addEventListener('click', async (e) => {
      e.preventDefault();
      const selectedFileId = parseInt(document.getElementById('editSegmentFile').value, 10);
      const fileResult = await filePicker({
        title: localize({ key: 'blog.posts.create.header_image' }),
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
    document.getElementById('cancel-edit-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('editSegmentHasLink').addEventListener('change', () => {
      const hasLink = document.getElementById('editSegmentHasLink').checked;
      document.getElementById('editSegmentLink').hidden = !hasLink;
      document.querySelector('[for="editSegmentLink"]').hidden = !hasLink;
    });
    document.getElementById('edit-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const fileId = parseInt(document.getElementById('editSegmentFile').value, 10);
      const link = document.getElementById('editSegmentLink').value;
      this.onHide({
        position: this.position,
        file: files.find((g) => g.id === fileId),
        link,
      });
      container.remove();
    });
  }
}
