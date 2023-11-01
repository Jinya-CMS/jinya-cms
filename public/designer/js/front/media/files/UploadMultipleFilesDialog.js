import html from '../../../../lib/jinya-html.js';
import dataUrlReader from '../../../foundation/blob/dataUrlReader.js';
import localize from '../../../foundation/localize.js';
import { getRandomColor, getRandomEmoji } from './tagUtils.js';
import { post } from '../../../foundation/http/request.js';
import alert from '../../../foundation/ui/alert.js';

export default class UploadMultipleFilesDialog {
  /**
   * Shows the upload dialog
   * @param onHide {function({files: FileList, tags: string[]})}
   * @param tags {{id: number, name: string, emoji: string, color: string}[]}
   * @param activeTagNames {string[]}
   */
  constructor({ tags, activeTagNames, onHide }) {
    this.tags = tags;
    this.activeTagNames = activeTagNames;
    this.onHide = onHide;
  }

  renderTags() {
    const tagList = document.getElementById('mfu-tag-list');
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
      <button class="cosmo-circular-button cosmo-circular-button--small" id="new-tag-open-button">
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
      document.getElementById('mfu-new-tag').open = true;
    });
  }

  show() {
    const content = html` <div class="cosmo-modal__backdrop"></div>
      <div class="cosmo-modal__container">
        <div class="cosmo-modal jinya-modal--multiple-files">
          <h1 class="cosmo-modal__title">${localize({ key: 'media.files.upload_multiple_files.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="uploadMultipleFilesPicker" class="cosmo-label">
                ${localize({ key: 'media.files.upload_multiple_files.files' })}
              </label>
              <input class="cosmo-input" multiple required type="file" id="uploadMultipleFilesPicker" />
              <label class="cosmo-label">${localize({ key: 'media.files.upload_multiple_files.tags' })}</label>
              <div class="jinya-tags jinya-tag--details" id="mfu-tag-list"></div>
              <cms-tag-popup
                id="mfu-new-tag"
                popup-title="${localize({ key: 'media.files.tags.new.title' })}"
                save-label="${localize({ key: 'media.files.tags.new.save' })}"
                cancel-label="${localize({ key: 'media.files.tags.new.cancel' })}"
                color="${getRandomColor()}"
                target="#new-tag-open-button"
                emoji="${getRandomEmoji()}"
              ></cms-tag-popup>
            </div>
            <div class="jinya-media-tile__container--modal"></div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button class="cosmo-button" id="cancel-upload-dialog">
              ${localize({ key: 'media.files.upload_multiple_files.cancel' })}
            </button>
            <button class="cosmo-button" id="save-upload-dialog">
              ${localize({ key: 'media.files.upload_multiple_files.upload' })}
            </button>
          </div>
        </div>
      </div>`;

    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);

    this.renderTags();

    document.getElementById('mfu-new-tag').addEventListener('submit', async (evt) => {
      try {
        const data = { emoji: evt.emoji, name: evt.name, color: evt.color };
        const result = await post('/api/file-tag', data);
        this.tags.push(result);
        this.renderTags();

        const popup = document.getElementById('mfu-new-tag');
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
    document.getElementById('uploadMultipleFilesPicker').addEventListener('change', async (e) => {
      const tileContainer = document.querySelector('.jinya-media-tile__container--modal');
      const selectedFiles = e.currentTarget.files;
      for (const selectedFile of selectedFiles) {
        // eslint-disable-next-line no-await-in-loop
        const src = await dataUrlReader({ file: selectedFile });
        const tile = document.createElement('div');
        tile.classList.add('jinya-media-tile', 'jinya-media-tile--small');
        tile.innerHTML = `<img class='jinya-media-tile__img jinya-media-tile__img--small' src='${src}'>`;
        tileContainer.append(tile);
      }
    });
    document.getElementById('cancel-upload-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('save-upload-dialog').addEventListener('click', () => {
      this.onHide({ files: document.getElementById('uploadMultipleFilesPicker').files, tags: this.activeTagNames });
      container.remove();
    });
  }
}
