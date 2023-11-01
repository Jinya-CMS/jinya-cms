import html from '../../../../lib/jinya-html.js';
import { get, httpDelete, post, put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import '../../../foundation/ui/components/tag-popup.js';
import '../../../foundation/ui/components/tag.js';
import confirm from '../../../foundation/ui/confirm.js';
import { getRandomColor, getRandomEmoji } from './tagUtils.js';

export default class ManageTagsDialog {
  /**
   * Shows the manage tags dialog
   * @param onHide {function()}
   */
  constructor({ onHide }) {
    this.tags = { items: [] };
    this.onHide = onHide;
  }

  renderTags() {
    document.getElementById('taglist').innerHTML = html` ${this.tags.items.map(
        (tag) =>
          html` <cms-tag
              emoji="${tag.emoji}"
              name="${tag.name}"
              color="${tag.color}"
              deletable
              editable
              tag-id="${tag.id}"
              id="tag-${tag.id}"
            ></cms-tag>
            <cms-tag-popup
              popup-title="${localize({ key: 'media.files.tags.edit.title' })}"
              save-label="${localize({ key: 'media.files.tags.edit.save' })}"
              cancel-label="${localize({ key: 'media.files.tags.edit.cancel' })}"
              color="${tag.color}"
              target="#tag-${tag.id}"
              name="${tag.name}"
              emoji="${tag.emoji}"
              id="tag-popup-${tag.id}"
            >
            </cms-tag-popup>`,
      )}
      <button class="cosmo-circular-button cosmo-circular-button--small" id="new-tag-open-button">
        <span class="mdi mdi-plus"></span>
      </button>`;
  }

  bindTagsEvents() {
    document.getElementById('new-tag-open-button').addEventListener('click', () => {
      document.querySelectorAll('cms-tag-popup').forEach((popup) => {
        // eslint-disable-next-line no-param-reassign
        popup.open = false;
      });

      const newTagPopup = document.getElementById('new-tag');
      newTagPopup.open = !newTagPopup.open;
    });
    document.querySelectorAll('cms-tag').forEach((cmsTag) => {
      cmsTag.addEventListener('edit', (evt) => {
        document.querySelectorAll('cms-tag-popup').forEach((popup) => {
          // eslint-disable-next-line no-param-reassign
          popup.open = false;
        });

        const popup = document.getElementById(`tag-popup-${evt.id}`);
        popup.open = !popup.open;

        popup.addEventListener('submit', async (event) => {
          try {
            const data = { emoji: event.emoji, name: event.name, color: event.color };
            await put(`/api/file-tag/${evt.id}`, data);

            this.tags = await get('/api/file-tag');
            this.renderTags();
            this.bindTagsEvents();
          } catch (e) {
            await alert({
              title: localize({ key: 'media.files.tags.edit.error.title' }),
              message: localize({
                key: `media.files.tags.edit.error.${e.status === 409 ? 'exists' : 'generic'}`,
              }),
              buttonLabel: localize({ key: 'media.files.tags.edit.error.close' }),
            });
          }
        });
      });
      cmsTag.addEventListener('delete', async (evt) => {
        if (
          await confirm({
            title: localize({ key: 'media.files.tags.delete.title' }),
            message: localize({ key: 'media.files.tags.delete.message', values: { name: evt.name } }),
            declineLabel: localize({ key: 'media.files.tags.delete.decline' }),
            approveLabel: localize({ key: 'media.files.tags.delete.approve' }),
          })
        ) {
          try {
            await httpDelete(`/api/file-tag/${evt.id}`);

            this.tags = await get('/api/file-tag');
            this.renderTags();
            this.bindTagsEvents();
          } catch (e) {
            await alert({
              title: localize({ key: 'media.files.tags.delete.error.title' }),
              message: localize({ key: 'media.files.tags.delete.error.generic' }),
              buttonLabel: localize({ key: 'media.files.tags.delete.error.close' }),
            });
          }
        }
      });
    });
  }

  async show() {
    const content = html` <div class="cosmo-modal__backdrop"></div>
      <div class="cosmo-modal__container" id="manage-tags-dialog">
        <div class="cosmo-modal cosmo-modal--tags">
          <h1 class="cosmo-modal__title">${localize({ key: 'media.files.tags.manage.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="jinya-tags" id="taglist"></div>
            <cms-tag-popup
              id="new-tag"
              popup-title="${localize({ key: 'media.files.tags.new.title' })}"
              save-label="${localize({ key: 'media.files.tags.new.save' })}"
              cancel-label="${localize({ key: 'media.files.tags.new.cancel' })}"
              color="${getRandomColor()}"
              target="#new-tag-open-button"
              emoji="${getRandomEmoji()}"
            >
            </cms-tag-popup>
          </div>
          <div class="cosmo-modal__button-bar">
            <button type="button" class="cosmo-button" id="close-manage-dialog">
              ${localize({ key: 'media.files.tags.manage.close' })}
            </button>
          </div>
        </div>
      </div>`;

    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('close-manage-dialog').addEventListener('click', () => {
      container.remove();
      this.onHide();
    });
    document.getElementById('new-tag').addEventListener('submit', async (evt) => {
      try {
        const data = { emoji: evt.emoji, name: evt.name, color: evt.color };
        const result = await post('/api/file-tag', data);
        this.tags.items.push(result);
        this.renderTags();
        this.bindTagsEvents();

        const popup = document.getElementById('new-tag');
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

    this.tags = await get('/api/file-tag');
    this.renderTags();
    this.bindTagsEvents();
  }
}
