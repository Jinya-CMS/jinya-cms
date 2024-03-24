import html from '../../../../lib/jinya-html.js';
import { get, post, put, upload } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import { getRandomColor, getRandomEmoji } from './tagUtils.js';

export default class EditDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({id: number, name: string, path: string, tags: {id: number, name: string, emoji: string, color: string}[]})}
   * @param tags {{id: number, name: string, emoji: string, color: string}[]}
   * @param file {{id: number, name: string, tags: {id: number, name: string, emoji: string, color: string}[]}}
   */
  constructor({
                onHide,
                file,
                tags,
              }) {
    this.onHide = onHide;
    this.file = file;
    this.tags = tags;
    this.activeTagNames = file.tags.map((m) => m.name);
  }

  renderTags() {
    const tagList = document.getElementById('edit-tag-list');
    tagList.innerHTML = html`${this.tags.map((tag) => html`
      <cms-tag
        emoji="${tag.emoji}"
        name="${tag.name}"
        color="${tag.color}"
        tag-id="${tag.id}"
        id="tag-${tag.name}"
        class="jinya-tag--file"
        ${this.activeTagNames.includes(tag.name) ? 'active' : ''}
      ></cms-tag>`)}
    <button class="cosmo-button is--small is--circle is--primary" id="new-tag-open-button" type="button">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
           stroke-linejoin="round">
        <path d="M5 12h14" />
        <path d="M12 5v14" />
      </svg>
    </button>`;
    tagList.querySelectorAll('cms-tag')
      .forEach((tag) => {
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
    document.getElementById('new-tag-open-button')
      .addEventListener('click', () => {
        document.getElementById('edit-new-tag').open = true;
      });
  }

  show() {
    const content = html`
      <div class="cosmo-modal__backdrop"></div>
      <form class="cosmo-modal__container" id="edit-dialog-form">
        <div class="cosmo-modal cosmo-modal--files">
          <h1 class="cosmo-modal__title">${localize({ key: 'media.files.edit.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="editFileName" class="cosmo-label"> ${localize({ key: 'media.files.edit.name' })} </label>
              <input required type="text" id="editFileName" class="cosmo-input" value="${this.file.name}" />
              <label for="editFileName" class="cosmo-label"> ${localize({ key: 'media.files.edit.file' })} </label>
              <input class="cosmo-input" type="file" id="editFileFile" />
              <label class="cosmo-label">${localize({ key: 'media.files.edit.tags' })}</label>
              <div class="jinya-tags jinya-tag--details" id="edit-tag-list"></div>
              <cms-tag-popup
                id="edit-new-tag"
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
            <button type="button" class="cosmo-button" id="cancel-edit-dialog">
              ${localize({ key: 'media.files.edit.cancel' })}
            </button>
            <button type="submit" class="cosmo-button" id="save-edit-dialog">
              ${localize({ key: 'media.files.edit.save' })}
            </button>
          </div>
        </div>
      </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);

    this.renderTags();

    document.getElementById('edit-new-tag')
      .addEventListener('submit', async (evt) => {
        try {
          const data = {
            emoji: evt.emoji,
            name: evt.name,
            color: evt.color,
          };
          const result = await post('/api/file-tag', data);
          this.tags.push(result);
          this.renderTags();

          const popup = document.getElementById('edit-new-tag');
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
            negative: true,
          });
        }
      });
    document.getElementById('cancel-edit-dialog')
      .addEventListener('click', () => {
        container.remove();
      });
    document.getElementById('edit-dialog-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('editFileName').value;
        const { files } = document.getElementById('editFileFile');
        try {
          await put(`/api/media/file/${this.file.id}`, {
            name,
            tags: this.activeTagNames,
          });
          if (files && files.length === 1) {
            await put(`/api/media/file/${this.file.id}/content`);
            await upload(`/api/media/file/${this.file.id}/content/0`, files[0]);
            await put(`/api/media/file/${this.file.id}/content/finish`);
          }
          const saved = await get(`/api/media/file/${this.file.id}`);
          this.onHide(saved);
          container.remove();
        } catch (err) {
          if (err.status === 409) {
            await alert({
              title: localize({ key: 'media.files.edit.error.title' }),
              message: localize({ key: 'media.files.edit.error.conflict' }),
              negative: true,
            });
          } else {
            await alert({
              title: localize({ key: 'media.files.edit.error.title' }),
              message: localize({ key: 'media.files.edit.error.generic' }),
              negative: true,
            });
          }
        }
      });
  }
}
