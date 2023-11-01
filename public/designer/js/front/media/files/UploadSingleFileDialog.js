import html from '../../../../lib/jinya-html.js';
import { get, post, put, upload } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import { getRandomColor, getRandomEmoji } from './tagUtils.js';

export default class UploadDialog {
  /**
   * Shows the upload dialog
   * @param onHide {function({id: number, name: string, path: string, tags: {id: number, name: string, emoji: string, color: string}[]})}
   * @param tags {{id: number, name: string, emoji: string, color: string}[]}
   * @param activeTagNames {string[]}
   */
  constructor({ tags, activeTagNames, onHide }) {
    this.tags = tags;
    this.activeTagNames = activeTagNames;
    this.onHide = onHide;
  }

  renderTags() {
    const tagList = document.getElementById('sfu-tag-list');
    tagList.innerHTML = html`${this.tags.map(
        (tag) =>
          html` <cms-tag
            emoji="${tag.emoji}"
            name="${tag.name}"
            color="${tag.color}"
            tag-id="${tag.id}"
            id="tag-${tag.name}"
            class="jinya-tag--file"
            ${this.activeTagNames.includes(tag.name) ? 'active' : ''}
          ></cms-tag>`,
      )}
      <button class="cosmo-circular-button cosmo-circular-button--small" id="new-tag-open-button" type="button">
        <span class="mdi mdi-plus"></span>
      </button>`;
    tagList.querySelectorAll('cms-tag').forEach((tag) => {
      tag.addEventListener('click', () => {
        // eslint-disable-next-line no-param-reassign
        tag.active = !tag.active;
        if (tag.active) {
          this.activeTagNames.push(tag.name);
        } else {
          this.activeTagNames = this.activeTagNames.filter((t) => tag.name !== t);
        }
      });
    });
    document.getElementById('new-tag-open-button').addEventListener('click', () => {
      document.getElementById('sfu-new-tag').open = true;
    });
  }

  show() {
    const content = html` <div class="cosmo-modal__backdrop"></div>
      <form class="cosmo-modal__container" id="upload-dialog-form">
        <div class="cosmo-modal cosmo-modal--files">
          <h1 class="cosmo-modal__title">${localize({ key: 'media.files.upload_single_file.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="uploadFileName" class="cosmo-label">
                ${localize({ key: 'media.files.upload_single_file.name' })}
              </label>
              <input required type="text" id="uploadFileName" class="cosmo-input" />
              <label for="uploadFileName" class="cosmo-label">
                ${localize({ key: 'media.files.upload_single_file.file' })}
              </label>
              <input required class="cosmo-input" type="file" id="uploadFileFile" />
              <label class="cosmo-label">${localize({ key: 'media.files.upload_single_file.tags' })}</label>
              <div class="jinya-tags jinya-tag--details" id="sfu-tag-list"></div>
              <cms-tag-popup
                id="sfu-new-tag"
                popup-title="${localize({ key: 'media.files.tags.new.title' })}"
                save-label="${localize({ key: 'media.files.tags.new.save' })}"
                cancel-label="${localize({ key: 'media.files.tags.new.cancel' })}"
                color="${getRandomColor()}"
                target="#new-tag-open-button"
                emoji="${getRandomEmoji()}"
              ></cms-tag-popup>
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button class="cosmo-button" id="cancel-upload-dialog">
              ${localize({ key: 'media.files.upload_single_file.cancel' })}
            </button>
            <button type="submit" class="cosmo-button" id="save-upload-dialog">
              ${localize({ key: 'media.files.upload_single_file.upload' })}
            </button>
          </div>
        </div>
      </form>`;

    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);

    this.renderTags();

    document.getElementById('sfu-new-tag').addEventListener('submit', async (evt) => {
      try {
        const data = { emoji: evt.emoji, name: evt.name, color: evt.color };
        const result = await post('/api/file-tag', data);
        this.tags.push(result);
        this.renderTags();

        const popup = document.getElementById('sfu-new-tag');
        popup.emoji = getRandomEmoji();
        popup.color = getRandomColor();
        popup.name = '';
        popup.open = false;
      } catch (e) {
        await alert({
          title: localize({ key: 'media.files.tags.new.error.title' }),
          message: localize({
            key: `media.files.tags.new.error.${e.status === 409 ? 'exists' : 'generic'}`,
          }),
          buttonLabel: localize({ key: 'media.files.tags.new.error.close' }),
        });
      }
    });
    document.getElementById('uploadFileFile').addEventListener('change', (e) => {
      const nameInput = document.getElementById('uploadFileName');
      if (nameInput.value === '') {
        const file = e.currentTarget.files[0];
        nameInput.value = file.name.split('.').reverse().slice(1).reverse().join('.');
      }
    });
    document.getElementById('cancel-upload-dialog').addEventListener('click', () => {
      container.remove();
      this.onHide(null);
    });
    document.getElementById('upload-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('uploadFileName').value;
      const { files } = document.getElementById('uploadFileFile');
      try {
        const createdFile = await post('/api/media/file', { name, tags: this.activeTagNames });
        await put(`/api/media/file/${createdFile.id}/content`);
        await upload(`/api/media/file/${createdFile.id}/content/0`, files[0]);
        await put(`/api/media/file/${createdFile.id}/content/finish`);
        await this.onHide(await get(`/api/media/file/${createdFile.id}`));
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'media.files.upload_single_file.error.title' }),
            message: localize({ key: 'media.files.upload_single_file.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'media.files.upload_single_file.error.title' }),
            message: localize({ key: 'media.files.upload_single_file.error.generic' }),
          });
        }
      }
    });
  }
}
