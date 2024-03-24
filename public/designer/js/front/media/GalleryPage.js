import html from '../../../lib/jinya-html.js';
import Sortable from '../../../lib/sortable.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete, post, put } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';
import '../../foundation/ui/components/tag.js';

export default class GalleryPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.galleries = [];
    this.selectedGallery = {};
    this.files = [];
    this.resultSortable = null;
    this.toolboxSortable = null;
    this.tags = [];
    this.activeTags = new Set();
  }

  renderTags() {
    document.getElementById('designer-tags').innerHTML = html`
      <cms-tag
        class="jinya-tag--file"
        emoji=""
        name="${localize({ key: 'media.galleries.action.show_all_tags' })}"
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
            .querySelectorAll('#designer-toolbox .jinya-media-tile')
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
          document.querySelectorAll('#designer-toolbox .jinya-media-tile')
            .forEach((tile) => {
              const file = this.files.find((f) => f.id === parseInt(tile.getAttribute('data-file-id'), 10));
              if (file.tags.filter((f) => this.activeTags.has(f.id)).length === 0) {
                tile.classList.add('jinya-media-tile--hidden');
              } else {
                tile.classList.remove('jinya-media-tile--hidden');
              }
            });

          if (allTags.active) {
            this.activeTags.clear();
            document.querySelectorAll('#designer-toolbox .jinya-media-tile')
              .forEach((tile) => {
                tile.classList.remove('jinya-media-tile--hidden');
              });
          }
        }
      }));
  }

  selectGallery({ id }) {
    this.selectedGallery = this.galleries.find((f) => f.id === parseInt(id, 10));
    document
      .querySelectorAll('.cosmo-side-list__item.is--active')
      .forEach((item) => item.classList.remove('is--active'));
    document.querySelector(`[data-id="${id}"]`)
      .classList
      .add('is--active');
    document.getElementById('edit-gallery-button').disabled = false;
    document.getElementById('delete-gallery-button').disabled = false;
    this.activeTags.clear();
    document.querySelectorAll('#designer-toolbox .jinya-media-tile')
      .forEach((tile) => {
        tile.classList.remove('jinya-media-tile--hidden');
      });
  }

  async displaySelectedGallery() {
    if (this.resultSortable) {
      this.resultSortable.destroy();
    }
    if (this.toolboxSortable) {
      this.toolboxSortable.destroy();
    }

    const {
      id,
      name,
      type,
      orientation,
    } = this.selectedGallery;
    document.getElementById('id-and-name').innerText = `#${id} ${name}`;
    document.getElementById('type-and-orientation').innerText = localize({
      key: `media.galleries.designer.title.${orientation.toLowerCase()}_${type.toLowerCase()}`,
    });

    const toolbox = document.getElementById('designer-toolbox');
    const result = document.getElementById('designer-result');
    clearChildren({ parent: toolbox });
    clearChildren({ parent: result });

    const positions = await get(`/api/media/gallery/${id}/file`);
    for (const position of positions) {
      const child = document.createElement('img');
      child.classList.add('jinya-media-tile', 'jinya-media-tile--designer', 'jinya-media-tile--draggable');
      child.src = position.file.path;
      child.alt = position.file.name;
      child.setAttribute('data-position', position.position);
      child.setAttribute('data-file-id', position.file.id);
      result.append(child);
    }

    for (const file of this.files.filter((f) => !positions.find((p) => p.file.id === f.id))) {
      const child = document.createElement('img');
      child.classList.add('jinya-media-tile', 'jinya-media-tile--designer', 'jinya-media-tile--medium', 'jinya-media-tile--draggable');
      child.src = file.path;
      child.alt = file.name;
      child.setAttribute('data-file-id', file.id);
      toolbox.append(child);
    }

    this.resultSortable = new Sortable(document.getElementById('designer-result'), {
      group: 'gallery',
      sort: true,
      onStart: async (e) => {
        e.item.classList.remove('jinya-media-tile--medium');
      },
      onAdd: async (e) => {
        e.item.classList.remove('jinya-media-tile--medium');
        const file = e.item.getAttribute('data-file-id');
        const dropIdx = e.newIndex;
        let position;
        if (positions.length === 0) {
          position = 0;
        } else if (positions.length === dropIdx) {
          position = positions[positions.length - 1].position + 2;
        } else {
          position = positions[dropIdx].position;
        }
        this.resetPositions();
        await post(`/api/media/gallery/${this.selectedGallery.id}/file`, {
          file: parseInt(file, 10),
          position,
        });
      },
      onUpdate: async (e) => {
        const oldPosition = e.item.getAttribute('data-position');
        const dropIdx = e.newIndex;
        this.resetPositions();
        await put(`/api/media/gallery/${this.selectedGallery.id}/file/${oldPosition}`, {
          newPosition: dropIdx > oldPosition ? dropIdx + 1 : dropIdx,
        });
      },
    });
    this.toolboxSortable = new Sortable(document.getElementById('designer-toolbox'), {
      group: 'gallery',
      sort: false,
      onAdd: async (e) => {
        e.item.classList.add('jinya-media-tile--medium');
        const position = e.item.getAttribute('data-position');
        this.resetPositions();
        await httpDelete(`/api/media/gallery/${this.selectedGallery.id}/file/${position}`);
      },
    });

    document.querySelectorAll('cms-tag')
      .forEach((tag) => {
        // eslint-disable-next-line no-param-reassign
        tag.active = false;
      });
    document.getElementById('show-all-tags').active = true;
  }

  // eslint-disable-next-line class-methods-use-this
  resetPositions() {
    document
      .getElementById('designer-result')
      .querySelectorAll('[data-position]')
      .forEach((elem, key) => {
        elem.setAttribute('data-position', key.toString(10));
      });
  }

  displayGalleries() {
    document.getElementById('edit-gallery-button').disabled = true;
    document.getElementById('delete-gallery-button').disabled = true;
    let list = '';
    for (const gallery of this.galleries) {
      list += `<a class="cosmo-side-list__item" data-id="${gallery.id}">${gallery.name}</a>`;
    }
    clearChildren({ parent: document.getElementById('gallery-list') });
    document.getElementById('gallery-list').innerHTML = `${list}
                <button id="new-gallery-button" class="cosmo-button is--full-width">
                    ${localize({ key: 'media.galleries.action.new' })}
                </button>`;
    document.querySelectorAll('.cosmo-side-list__item')
      .forEach((item) => {
        item.addEventListener('click', async () => {
          this.selectGallery({ id: item.getAttribute('data-id') });
          await this.displaySelectedGallery();
        });
      });
    document.getElementById('new-gallery-button')
      .addEventListener('click', async () => {
        const { default: AddGalleryDialog } = await import('./galleries/AddGalleryDialog.js');
        const dialog = new AddGalleryDialog({
          onHide: async (gallery) => {
            this.galleries.push(gallery);
            this.displayGalleries();
            this.selectGallery({ id: gallery.id });
            await this.displaySelectedGallery();
          },
        });
        dialog.show();
      });
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
      <div class="cosmo-side-list">
        <nav class="cosmo-side-list__items" id="gallery-list">
          <button class="cosmo-button is--full-width">
            ${localize({ key: 'media.galleries.action.new' })}
          </button>
        </nav>
        <div class="cosmo-side-list__content jinya-designer" id="gallery-designer">
          <div class="jinya-designer__title">
            <span class="cosmo-title" id="id-and-name"></span>
            <span class="cosmo-title" id="type-and-orientation"></span>
          </div>
          <div class="cosmo-toolbar cosmo-toolbar--designer">
            <div class="cosmo-toolbar__group">
              <button id="edit-gallery-button" class="cosmo-button">
                ${localize({ key: 'media.galleries.action.edit' })}
              </button>
              <button id="delete-gallery-button" class="cosmo-button">
                ${localize({ key: 'media.galleries.action.delete' })}
              </button>
            </div>
          </div>
          <div class="jinya-designer__content">
            <div class="jinya-designer__result" id="designer-result"></div>
            <div class="jinya-designer__toolbox-container">
              <div class="jinya-designer__tags" id="designer-tags"></div>
              <div class="jinya-designer__toolbox" id="designer-toolbox"></div>
            </div>
          </div>
        </div>
      </div>`;
  }

  async displayed() {
    await super.displayed();
    await Promise.all([(async () => {
      const { items } = await get('/api/media/gallery');
      this.galleries = items;
    })(), (async () => {
      const { items } = await get('/api/media/file');
      this.files = items;
    })(), (async () => {
      const { items } = await get('/api/file-tag');
      this.tags = items;
    })()]);
    this.displayGalleries();
    this.renderTags();
    if (this.galleries.length !== 0) {
      this.selectGallery(this.galleries[0]);
      await this.displaySelectedGallery();
    }
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('delete-gallery-button')
      .addEventListener('click', async () => {
        const confirmation = await confirm({
          title: localize({ key: 'media.galleries.delete.title' }),
          message: localize({
            key: 'media.galleries.delete.message',
            values: this.selectedGallery,
          }),
          declineLabel: localize({ key: 'media.galleries.delete.keep' }),
          approveLabel: localize({ key: 'media.galleries.delete.delete' }),
        });
        if (confirmation) {
          try {
            await httpDelete(`/api/media/gallery/${this.selectedGallery.id}`);
            this.galleries = this.galleries.filter((gallery) => gallery.id !== this.selectedGallery.id);
            this.displayGalleries();
            if (this.galleries.length > 0) {
              this.selectGallery({ id: this.galleries[0].id });
              await this.displaySelectedGallery();
            } else {
              this.selectedGallery = null;
              document.getElementById('id-and-name').innerText = '';
              document.getElementById('type-and-orientation').innerText = '';
            }
          } catch (e) {
            if (e.status === 409) {
              await alert({
                title: localize({ key: 'media.galleries.delete.error.title' }),
                message: localize({ key: 'media.galleries.delete.error.conflict' }),
              });
            } else {
              await alert({
                title: localize({ key: 'media.galleries.delete.error.title' }),
                message: localize({ key: 'media.galleries.delete.error.generic' }),
              });
            }
          }
        }
      });
    document.getElementById('edit-gallery-button')
      .addEventListener('click', async () => {
        const { default: EditGalleryDialog } = await import('./galleries/EditGalleryDialog.js');
        const dialog = new EditGalleryDialog({
          gallery: this.selectedGallery,
          onHide: async (gallery) => {
            this.galleries[this.galleries.findIndex((item) => item.id === gallery.id)] = gallery;
            this.displayGalleries();
            this.selectGallery({ id: gallery.id });
            await this.displaySelectedGallery();
          },
        });
        dialog.show();
      });
  }
}
