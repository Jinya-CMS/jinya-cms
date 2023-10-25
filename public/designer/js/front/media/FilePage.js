import html from '../../../lib/jinya-html.js';
import { get, httpDelete } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';
import FilesSelectedEvent from './files/FilesSelectedEvent.js';
import MimeTypes from '../../../lib/mime/types.js';
import ManageTagsDialog from './files/ManageTagsDialog.js';

export default class FilePage extends JinyaDesignerPage {
  /**
   * @param layout {JinyaDesignerLayout}
   */
  constructor({ layout }) {
    super({ layout });
    this.files = [];
    this.loading = true;
    this.selectedFile = null;
    document.addEventListener('fileUploaded', async ({ file: { id, name, path } }) => {
      const newTile = document.createElement('div');
      newTile.setAttribute('data-id', id.toString(10));
      newTile.setAttribute('data-title', `${id} ${name}`);
      newTile.classList.add('jinya-media-tile');
      newTile.innerHTML = `<img class="jinya-media-tile__img" src="${path}" alt="${name}">`;
      document.querySelector('.jinya-media-tile__container').append(newTile);
      newTile.addEventListener('click', this.tileClicked(newTile));
      this.files.push(await get(`/api/media/file/${id}`));
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
      view = html` <div class="jinya-media-view__loader jinya-loader__container">
        <div class="jinya-loader"></div>
      </div>`;
    } else {
      view = html` <div class="jinya-media-view__container">
        <div class="jinya-media-tile__container">
          ${this.files.map(
            (file) =>
              html` <div data-id="${file.id}" data-title="${file.id} ${file.name}" class="jinya-media-tile">
                <img class="jinya-media-tile__img" src="${file.path}" alt="${file.name}" />
              </div>`,
          )}
        </div>
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
        ${view}
      </div>
    `;
  }

  bindEvents() {
    super.bindEvents();
    if (!this.loading) {
      document.querySelectorAll('.jinya-media-tile').forEach((tile) => {
        tile.addEventListener('click', this.tileClicked(tile));
      });
      document.getElementById('delete-file').addEventListener('click', async () => {
        const selectedTile = document.querySelector(`[data-id="${this.selectedFile.id}"].jinya-media-tile`);
        const deleteConfirmation = await confirm({
          title: localize({ key: 'media.files.delete.title' }),
          message: localize({
            key: 'media.files.delete.message',
            values: { name: this.selectedFile.name },
          }),
          declineLabel: localize({ key: 'media.files.delete.decline' }),
          approveLabel: localize({ key: 'media.files.delete.approve' }),
        });
        if (deleteConfirmation) {
          try {
            await httpDelete(`/api/media/file/${this.selectedFile.id}`);
            selectedTile.remove();
            this.files = this.files.filter((file) => file.id !== this.selectedFile.id);
            this.selectedFile = null;
          } catch (e) {
            if (e.status === 409) {
              await alert({
                title: localize({ key: 'media.files.delete.error.title' }),
                message: localize({
                  key: 'media.files.delete.error.conflict',
                  values: { name: this.selectedFile.name },
                }),
              });
            } else {
              await alert({
                title: localize({ key: 'media.files.delete.error.title' }),
                message: localize({ key: 'media.files.delete.error.generic' }),
              });
            }
          }
        }
      });
      document.getElementById('edit-file').addEventListener('click', async () => {
        const { default: EditDialog } = await import('./files/EditDialog.js');
        const editDialog = new EditDialog({
          onHide: async ({ id, name, path }) => {
            const selectedTile = document.querySelector(`[data-id="${id}"].jinya-media-tile`);
            selectedTile.setAttribute('data-title', `${id} ${name}`);
            const tileImage = document.querySelector(`[data-id="${id}"].jinya-media-tile .jinya-media-tile__img`);
            tileImage.alt = name;
            tileImage.src = path;
            this.selectedFile.name = name;
            this.selectedFile.path = path;

            const changedFile = await get(`/api/media/file/${id}`);
            this.selectedFile.type = changedFile.type;
            this.selectedFile.created = changedFile.created;
            this.selectedFile.updated = changedFile.updated;

            this.showDetails();
          },
          file: this.selectedFile,
        });
        editDialog.show();
      });
      document.getElementById('upload-single-file').addEventListener('click', async () => {
        const { default: UploadSingleFileDialog } = await import('./files/UploadSingleFileDialog.js');
        const uploadSingleFileDialog = new UploadSingleFileDialog({
          onHide: async ({ id, name, path }) => {
            const newTile = document.createElement('div');
            newTile.setAttribute('data-id', id.toString(10));
            newTile.setAttribute('data-title', `${id} ${name}`);
            newTile.classList.add('jinya-media-tile');
            newTile.innerHTML = `<img class="jinya-media-tile__img" src="${path}" alt="${name}">`;
            document.querySelector('.jinya-media-tile__container').append(newTile);
            newTile.addEventListener('click', this.tileClicked(newTile));
            this.files.push(await get(`/api/media/file/${id}`));
          },
        });
        uploadSingleFileDialog.show();
      });
      document.getElementById('upload-multiple-files').addEventListener('click', async () => {
        const { default: UploadMultipleFilesDialog } = await import('./files/UploadMultipleFilesDialog.js');
        const dialog = new UploadMultipleFilesDialog({
          onHide: ({ files }) => {
            document.dispatchEvent(new FilesSelectedEvent({ files }));
          },
        });
        dialog.show();
      });
      document.getElementById('manage-tags').addEventListener('click', async () => {
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
      document.getElementById('edit-file').removeAttribute('disabled');
      document.getElementById('delete-file').removeAttribute('disabled');

      this.showDetails();
    };
  }

  showDetails() {
    document.querySelector('.jinya-media-view__details').innerHTML = html`
      <span class="cosmo-title">${this.selectedFile.name}</span>
      ${() => {
        if (this.selectedFile.type.startsWith('image')) {
          return `<img 
                        alt="${this.selectedFile.name}" 
                        src="${this.selectedFile.path}" class="jinya-media-details__image">`;
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
      <dl class="cosmo-key-value-list">
        <dt class="cosmo-key-value-list__key">${localize({ key: 'media.files.details.type' })}</dt>
        <dd class="cosmo-key-value-list__value" title="${this.selectedFile.type}">
          ${FilePage.mapType(this.selectedFile.type)}
        </dd>
        <dt class="cosmo-key-value-list__key">${localize({ key: 'media.files.details.uploadedBy' })}</dt>
        <dd class="cosmo-key-value-list__value">
          ${this.selectedFile.created.by.artistName}
          <small>
            <a href="mailto:${this.selectedFile.created.by.email}">${this.selectedFile.created.by.email}</a>
          </small>
        </dd>
        <dt class="cosmo-key-value-list__key">${localize({ key: 'media.files.details.lastChangedBy' })}</dt>
        <dd class="cosmo-key-value-list__value">
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

  showTagManagementDialog() {
    const manageTags = new ManageTagsDialog();
    manageTags.show();
  }

  async displayed() {
    await super.displayed();
    if (this.loading) {
      const { items: files } = await get('/api/media/file');
      this.files = files;
      this.loading = false;
      this.display();
    }
  }
}
