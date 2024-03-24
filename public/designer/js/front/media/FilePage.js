import html from '../../../lib/jinya-html.js';
import { get, httpDelete } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';
import FilesSelectedEvent from './files/FilesSelectedEvent.js';
import MimeTypes from '../../../lib/mime/types.js';
import ManageTagsDialog from './files/ManageTagsDialog.js';
import '../../foundation/ui/components/tag.js';

function lightenDarkenColor(color, percentage) {
  const col = parseInt(color.toString()
    .replaceAll('#', ''), 16);
  // eslint-disable-next-line no-bitwise
  return (// eslint-disable-next-line no-bitwise
    (((col & 0x0000ff) + percentage) | // eslint-disable-next-line no-bitwise
      ((((col >> 8) & 0x00ff) + percentage) << 8) | // eslint-disable-next-line no-bitwise
      (((col >> 16) + percentage) << 16)).toString(16));
}

export default class FilePage extends JinyaDesignerPage {
  /**
   * @param layout {JinyaDesignerLayout}
   */
  constructor({ layout }) {
    super({ layout });
    this.files = [];
    this.loading = true;
    this.selectedFile = null;
    this.tags = [];
    this.activeTags = new Set();
    this.fileTile = this.fileTile.bind(this);
    document.addEventListener('fileUploaded', async ({ file }) => {
      document.querySelector('.jinya-media-tile__container')
        .insertAdjacentHTML('beforeend', this.fileTile(file));
      document
        .getElementById(`tile-${file.id}`)
        .addEventListener('click', this.tileClicked(document.getElementById(`tile-${file.id}`)));
      this.files.push(file);
    });
  }

  static mapType(type) {
    if (type.startsWith('font')) {
      return localize({ key: 'media.files.details.types.font' });
    }

    const localizedType = localize({ key: `media.files.details.types.${type}` });
    if (localizedType === `media.files.details.types.${type}`) {
      return type;
    }

    return localizedType;
  }

  toString() {
    let view = '';
    if (this.loading) {
      view = html`
        <div class="jinya-media-view__loader jinya-loader__container">
          <div class="jinya-loader"></div>
        </div>`;
    } else {
      view = html`
        <div class="jinya-media-view__container">
          <div class="jinya-media-tile__container">${this.files.map(this.fileTile)}</div>
          <div class="jinya-media-view__details"></div>
        </div>`;
    }

    return html`
      <div class="jinya-media-view">
        <div class="cosmo-toolbar jinya-toolbar--media">
          <div class="cosmo-toolbar__group">
            <button class="cosmo-button" id="upload-single-file" type="button">
              ${localize({ key: 'media.files.action.upload_single_file' })}
            </button>
            <button class="cosmo-button" id="upload-multiple-files" type="button">
              ${localize({ key: 'media.files.action.upload_multiple_file' })}
            </button>
          </div>
          <div class="cosmo-toolbar__group">
            <button disabled class="cosmo-button" id="edit-file" type="button">
              ${localize({ key: 'media.files.action.edit_file' })}
            </button>
            <button disabled class="cosmo-button" id="delete-file" type="button">
              ${localize({ key: 'media.files.action.delete_file' })}
            </button>
          </div>
          <div class="cosmo-toolbar__group">
            <button class="cosmo-button" id="manage-tags" type="button">
              ${localize({ key: 'media.files.action.manage_tags' })}
            </button>
          </div>
        </div>
        <div id="tag-list" class="jinya-tags jinya-tags--media"></div>
        ${view}
      </div>
    `;
  }

  bindEvents() {
    super.bindEvents();
    if (!this.loading) {
      document.querySelectorAll('.jinya-media-tile')
        .forEach((tile) => {
          tile.addEventListener('click', this.tileClicked(tile));
        });
      document.getElementById('delete-file')
        ?.addEventListener('click', async () => {
          const selectedTile = document.querySelector(`[data-id="${this.selectedFile.id}"].jinya-media-tile`);
          const deleteConfirmation = await confirm({
            title: localize({ key: 'media.files.delete.title' }),
            message: localize({
              key: 'media.files.delete.message',
              values: { name: this.selectedFile.name },
            }),
            declineLabel: localize({ key: 'media.files.delete.decline' }),
            approveLabel: localize({ key: 'media.files.delete.approve' }),
            negative: true,
          });
          if (deleteConfirmation) {
            try {
              await httpDelete(`/api/media/file/${this.selectedFile.id}`);
              selectedTile.remove();
              this.files = this.files.filter((file) => file.id !== this.selectedFile.id);
              this.selectedFile = null;
              document.querySelector('.jinya-media-view__details').innerHTML = '';
            } catch (e) {
              if (e.status === 409) {
                await alert({
                  title: localize({ key: 'media.files.delete.error.title' }),
                  message: localize({
                    key: 'media.files.delete.error.conflict',
                    values: { name: this.selectedFile.name },
                  }),
                  negative: true,
                });
              } else {
                await alert({
                  title: localize({ key: 'media.files.delete.error.title' }),
                  message: localize({ key: 'media.files.delete.error.generic' }),
                  negative: true,
                });
              }
            }
          }
        });
      document.getElementById('edit-file')
        ?.addEventListener('click', async () => {
          const { default: EditDialog } = await import('./files/EditDialog.js');
          const editDialog = new EditDialog({
            tags: this.tags,
            onHide: async ({
                             id,
                             name,
                             path,
                             tags,
                             updated,
                             created,
                             type,
                           }) => {
              const selectedTile = document.querySelector(`[data-id="${id}"].jinya-media-tile`);
              selectedTile.setAttribute('data-title', `${id} ${name}`);
              selectedTile.querySelector('ul').innerHTML = html`${tags.map((tag) => html`
                <li
                  style="--tag-color: ${tag.color}; --tag-after-color: #${lightenDarkenColor(tag.color, -20)}"
                  class="jinya-tile__tag"
                >
                  <span class="jinya-tile__tag-arrow"></span>
                  <span class="jinya-tile__tag-emoji">${tag.emoji}</span>
                </li>`)}`;

              const tileImage = document.querySelector(`[data-id="${id}"].jinya-media-tile .jinya-media-tile__img`);
              tileImage.alt = name;
              tileImage.src = path;
              this.selectedFile.name = name;
              this.selectedFile.path = path;
              this.selectedFile.type = type;
              this.selectedFile.created = created;
              this.selectedFile.updated = updated;
              this.selectedFile.tags = tags;

              this.showDetails();
            },
            file: this.selectedFile,
          });
          editDialog.show();
        });
      document.getElementById('upload-single-file')
        ?.addEventListener('click', async () => {
          const { default: UploadSingleFileDialog } = await import('./files/UploadSingleFileDialog.js');
          const uploadSingleFileDialog = new UploadSingleFileDialog({
            tags: this.tags,
            activeTagNames: this.tags.filter((f) => this.activeTags.has(f.id))
              .map((m) => m.name),
            onHide: async (file) => {
              if (file) {
                document
                  .querySelector('.jinya-media-tile__container')
                  .insertAdjacentHTML('beforeend', this.fileTile(file));
                document
                  .getElementById(`tile-${file.id}`)
                  .addEventListener('click', this.tileClicked(document.getElementById(`tile-${file.id}`)));
                this.files.push(file);
                this.selectedFile = file;
                this.showDetails();
              }
              file.tags.forEach((t) => this.activeTags.add(t.id));
              await this.loadTags(false);
              this.renderTags();
            },
          });
          uploadSingleFileDialog.show();
        });
      document.getElementById('upload-multiple-files')
        ?.addEventListener('click', async () => {
          const { default: UploadMultipleFilesDialog } = await import('./files/UploadMultipleFilesDialog.js');
          const dialog = new UploadMultipleFilesDialog({
            tags: this.tags,
            activeTagNames: this.tags.filter((f) => this.activeTags.has(f.id))
              .map((m) => m.name),
            onHide: async ({
                             files,
                             tags,
                           }) => {
              document.dispatchEvent(new FilesSelectedEvent({
                files,
                tags,
              }));
              await this.loadTags(false);
              this.tags.forEach((tag) => {
                if (tags.includes(tag.name)) {
                  this.activeTags.add(tag.id);
                }
              });
              this.renderTags();
            },
          });
          dialog.show();
        });
      document.getElementById('manage-tags')
        ?.addEventListener('click', async () => {
          await this.showTagManagementDialog();
        });
    }
  }

  tileClicked(tile) {
    return () => {
      const id = parseInt(tile.getAttribute('data-id'), 10);
      this.selectedFile = this.files.find((file) => file.id === id);
      document
        .querySelectorAll('.jinya-media-tile--selected')
        .forEach((selectedTile) => selectedTile.classList.remove('jinya-media-tile--selected'));
      tile.classList.add('jinya-media-tile--selected');
      document.getElementById('edit-file')
        .removeAttribute('disabled');
      document.getElementById('delete-file')
        .removeAttribute('disabled');

      this.showDetails();
    };
  }

  showDetails() {
    document.querySelector('.jinya-media-view__details').innerHTML = html`
      <span class="cosmo-title">${this.selectedFile.name}</span>
      ${() => {
        if (this.selectedFile.type.startsWith('image')) {
          return `<img 
                        alt='${this.selectedFile.name}' 
                        src='${this.selectedFile.path}' class='jinya-media-details__image'>`;
        }
        if (this.selectedFile.type.startsWith('video')) {
          return html`
            <video controls class="jinya-media-details__image">
              <source src="${this.selectedFile.path}" type="${this.selectedFile.type}" />
            </video>
          `;
        }
        if (this.selectedFile.type.startsWith('audio')) {
          return html`
            <audio controls class="jinya-media-details__image">
              <source src="${this.selectedFile.path}" type="${this.selectedFile.type}" />
            </audio>
          `;
        }

        return '';
      }}
      <dl class="cosmo-list is--key-value">
        <dd class="cosmo-list__value--tags">
          ${this.selectedFile.tags.map((tag) => html`
            <cms-tag
              class="jinya-tag--details"
              emoji="${tag.emoji}"
              name="${tag.name}"
              color="${tag.color}"
              tag-id="${tag.id}"
              active
            ></cms-tag>`)}
        </dd>
        <dt>${localize({ key: 'media.files.details.type' })}</dt>
        <dd title="${this.selectedFile.type}">
          ${FilePage.mapType(this.selectedFile.type)}
        </dd>
        <dt>${localize({ key: 'media.files.details.uploadedBy' })}</dt>
        <dd>
          ${this.selectedFile.created.by.artistName}
          <small>
            <a href="mailto:${this.selectedFile.created.by.email}">${this.selectedFile.created.by.email}</a>
          </small>
        </dd>
        <dt>${localize({ key: 'media.files.details.lastChangedBy' })}</dt>
        <dd>
          ${this.selectedFile.updated.by.artistName}
          <small>
            <a href="mailto:${this.selectedFile.updated.by.email}">${this.selectedFile.updated.by.email}</a>
          </small>
        </dd>
      </dl>
      <a
        class="cosmo-button"
        download="${this.selectedFile.name}"
        href="${this.selectedFile.path}.${MimeTypes[this.selectedFile.type]?.extensions[0] ?? ''}"
      >
        ${localize({ key: 'media.files.details.downloadFile' })}
      </a>
    `;
  }

  async showTagManagementDialog() {
    const manageTags = new ManageTagsDialog({
      onHide: async () => {
        await this.loadTags();
        this.renderTags();
      },
    });
    await manageTags.show();
  }

  async loadTags(activateAll = false) {
    const { items } = await get('/api/file-tag');
    this.tags = items;
    if (activateAll) {
      this.activeTags.clear();
    }
  }

  renderTags() {
    document.getElementById('tag-list').innerHTML = html`
      <cms-tag
        class="jinya-tag--file"
        emoji=""
        name="${localize({ key: 'media.files.action.show_all_tags' })}"
        color="#19324c"
        tag-id="-1"
        id="show-all-tags"
        ${this.activeTags.size === 0 ? 'active' : ''}
      ></cms-tag>
      ${this.tags.map((tag) => html`
        <cms-tag
          class="jinya-tag--file"
          emoji="${tag.emoji}"
          name="${tag.name}"
          color="${tag.color}"
          tag-id="${tag.id}"
          id="show-tag-${tag.id}"
          ${this.activeTags.has(tag.id) ? 'active' : ''}
        ></cms-tag>`)}
    `;
    document.querySelectorAll('cms-tag')
      .forEach((tag) => tag.addEventListener('click', (evt) => {
        evt.stopPropagation();
        // eslint-disable-next-line no-param-reassign
        tag.active = !tag.active;
        if (tag.id === 'show-all-tags') {
          document
            .querySelectorAll('.jinya-media-tile')
            .forEach((tile) => tile.classList.remove('jinya-media-tile--hidden'));
          document.querySelectorAll('cms-tag')
            .forEach((t) => {
              // eslint-disable-next-line no-param-reassign
              t.active = false;
            });
          // eslint-disable-next-line no-param-reassign
          tag.active = true;
        } else {
          const allTags = document.getElementById('show-all-tags');
          if (tag.active) {
            this.activeTags.add(tag.tagId);
          } else {
            this.activeTags.delete(tag.tagId);
          }
          allTags.active = this.activeTags.size === 0 || this.activeTags.size === this.tags.length;
          document.querySelectorAll('.jinya-media-tile')
            .forEach((tile) => {
              const file = this.files.find((f) => f.id === parseInt(tile.getAttribute('data-id'), 10));
              if (file.tags.filter((f) => this.activeTags.has(f.id)).length === 0) {
                tile.classList.add('jinya-media-tile--hidden');
                if (file.id === this.selectedFile?.id) {
                  this.selectedFile = null;
                  document.querySelector('.jinya-media-view__details').innerHTML = '';
                }
              } else {
                tile.classList.remove('jinya-media-tile--hidden');
              }
            });

          if (allTags.active) {
            this.activeTags.clear();
            document.querySelectorAll('.jinya-media-tile')
              .forEach((tile) => {
                tile.classList.remove('jinya-media-tile--hidden');
              });
          }
        }
      }));
  }

  async displayed() {
    await super.displayed();

    if (this.loading) {
      const { items: files } = await get('/api/media/file');
      this.files = files;
      await this.loadTags(true);
      this.loading = false;
      this.display();
    }
  }

  async display() {
    await super.display();
    this.renderTags();
  }

  fileTile(file) {
    return html`
      <div
        data-id="${file.id}"
        data-title="${file.id} ${file.name}"
        id="tile-${file.id}"
        class="jinya-media-tile ${this.activeTags.size > 0 && file.tags.filter((f) => this.activeTags.has(f.id)) === 0 ? 'jinya-media-tile--hidden' : ''}"
      >
        <div class="jinya-media-tile__img">
          <img src="${file.path}" alt="${file.name}" />
        </div>
        <ul class="jinya-tile__tags">
          ${file.tags.map((tag) => html`
            <li
              style="--tag-color: ${tag.color}; --tag-after-color: #${lightenDarkenColor(tag.color, -20)}"
              class="jinya-tile__tag"
            >
              <span class="jinya-tile__tag-arrow"></span>
              <span class="jinya-tile__tag-emoji">${tag.emoji}</span>
            </li>`)}
        </ul>
      </div>`;
  }
}
