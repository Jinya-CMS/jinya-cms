import html from '../../../lib/jinya-html.js';
import Sortable from '../../../lib/sortable.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete, put } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';

export default class SegmentPagePage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.pages = [];
    this.selectedPage = {};
    this.selectedSegment = {};
    this.segments = [];
    this.resultSortable = null;
    this.toolboxSortable = null;
  }

  selectPage({ id }) {
    document.getElementById('edit-page').disabled = false;
    document.getElementById('delete-page').disabled = false;
    this.selectedPage = this.pages.find((p) => p.id === parseInt(id, 10));
    document
      .querySelectorAll('.cosmo-list__item--active')
      .forEach((item) => item.classList.remove('cosmo-list__item--active'));
    document.querySelector(`[data-id="${id}"]`).classList.add('cosmo-list__item--active');
    document.getElementById('edit-segment').setAttribute('disabled', 'disabled');
    document.getElementById('delete-segment').setAttribute('disabled', 'disabled');
  }

  async displaySelectedPage() {
    this.segments = await get(`/api/segment-page/${this.selectedPage.id}/segment`);
    this.displaySegments();
  }

  // eslint-disable-next-line class-methods-use-this
  resetPositions() {
    document.getElementById('segment-list')
      .querySelectorAll('[data-position]')
      .forEach((elem, key) => {
        elem.setAttribute('data-position', key.toString(10));
      });
  }

  selectSegment({ segmentPosition }) {
    document
      .querySelectorAll('.jinya-designer-item--selected')
      .forEach((item) => item.classList.remove('jinya-designer-item--selected'));
    document
      .querySelectorAll('.jinya-designer-item__details--file-selected')
      .forEach((item) => item.classList.remove('jinya-designer-item__details--file-selected'));
    document
      .querySelectorAll('.jinya-segment__image--file-selected')
      .forEach((item) => item.classList.remove('jinya-segment__image--file-selected'));

    const item = document.querySelector(`[data-position="${segmentPosition}"]`);
    item.classList.add('jinya-designer-item--selected');
    if (item.getAttribute('data-is-file') === 'true') {
      item.classList.add('jinya-designer-item--file-selected');
      item.querySelector('.jinya-designer-item__details')
        .classList.add('jinya-designer-item__details--file-selected');
      item.querySelector('.jinya-segment__image').classList.add('jinya-segment__image--file-selected');
    }

    document.getElementById('edit-segment').removeAttribute('disabled');
    document.getElementById('delete-segment').removeAttribute('disabled');
    this.selectedSegment = this.segments.find((s) => s.position === parseInt(segmentPosition, 10));
  }

  displaySegments() {
    if (this.resultSortable) {
      this.resultSortable.destroy();
    }
    document.getElementById('page-title').innerText = `#${this.selectedPage.id} ${this.selectedPage.name}`;
    const segmentList = document.getElementById('segment-list');
    clearChildren({ parent: segmentList });
    for (const segment of this.segments) {
      if (segment.html) {
        this.displayHtmlSegment({ segment, segmentList });
      } else if (segment.file) {
        this.displayFileSegment({ segment, segmentList });
      } else if (segment.gallery) {
        this.displayGallerySegment({ segment, segmentList });
      }
    }
    document.querySelectorAll('#segment-list .jinya-designer-item').forEach((item) => {
      item.addEventListener('click', () => {
        this.selectSegment({ segmentPosition: item.getAttribute('data-position') });
      });
    });
    this.resultSortable = new Sortable(document.getElementById('segment-list'), {
      group: { name: 'segment_page', put: true, pull: false },
      sort: true,
      onAdd: async (e) => {
        const dropIdx = e.newIndex;
        let createPosition = 0;
        if (this.segments.length === 0) {
          createPosition = 0;
        } else if (this.segments.length === dropIdx) {
          createPosition = this.segments[this.segments.length - 1].position + 2;
        } else {
          createPosition = this.segments[dropIdx].position;
        }
        const createNewSegmentType = e.item.getAttribute('data-type');
        await this.openSegmentEditorCreate({ position: createPosition, type: createNewSegmentType });
      },
      onUpdate: async (e) => {
        const oldPosition = e.item.getAttribute('data-position');
        const position = e.newIndex;
        this.resetPositions();
        await put(`/api/segment-page/${this.selectedPage.id}/segment/${oldPosition}`, {
          newPosition: position > oldPosition ? position + 1 : position,
        });
      },
    });
  }

  /**
   * @param segment {{id: number, position: number, html: string}}
   * @param segmentList {HTMLDivElement}
   */
  // eslint-disable-next-line class-methods-use-this
  displayHtmlSegment({ segment, segmentList }) {
    const segmentElem = document.createElement('div');
    segmentElem.classList.add('jinya-designer-item', 'jinya-designer-item--html');
    segmentElem.setAttribute('data-position', segment.position.toString(10));
    segmentElem.setAttribute('data-id', segment.id.toString(10));
    segmentElem.innerHTML = html`
        <span class="jinya-designer-item__title">
            ${localize({ key: 'pages_and_forms.segment.designer.html' })}
        </span>
        <div class="jinya-designer-item__details jinya-designer-item__details--html">${segment.html}</div>`;
    segmentList.appendChild(segmentElem);
  }

  /**
   * @param segment {{id: number, position: number, action: string, target: string, file: {name: string, path: string}}}
   * @param segmentList {HTMLDivElement}
   */
  // eslint-disable-next-line class-methods-use-this
  displayFileSegment({ segment, segmentList }) {
    const segmentElem = document.createElement('div');
    segmentElem.classList.add('jinya-designer-item', 'jinya-designer-item--file');
    segmentElem.setAttribute('data-position', segment.position.toString(10));
    segmentElem.setAttribute('data-id', segment.id.toString(10));
    segmentElem.setAttribute('data-is-file', 'true');
    segmentElem.innerHTML = html`
        <img class="jinya-segment__image" src="${segment.file.path}" alt="${segment.file.name}">
        <div class="jinya-designer-item__details jinya-designer-item__details--file">
            <span class="jinya-designer-item__title">
                ${localize({ key: 'pages_and_forms.segment.designer.file' })}
            </span>
            <dl class="jinya-segment__action">
                <dt class="jinya-segment__label">${localize({ key: 'pages_and_forms.segment.designer.action' })}</dt>
                <dd class="jinya-segment__content" data-type="action-label">
                    ${localize({ key: `pages_and_forms.segment.designer.action_${segment.action}` })}
                </dd>
                <dt class="jinya-segment__label" data-type="target"
                    ${segment.action !== 'link' ? 'style="display: none;"' : ''}>
                    ${localize({ key: 'pages_and_forms.segment.designer.link' })}
                </dt>
                <dd class="jinya-segment__content" data-type="target" data-action="target"
                    ${segment.action !== 'link' ? 'style="display: none;"' : ''}>
                    ${segment.target}
                </dd>
            </dl>
        </div>`;
    segmentList.appendChild(segmentElem);
  }

  /**
   * @param segment {{id: number, position: number, gallery: {name: string}}}
   * @param segmentList {HTMLDivElement}
   */
  // eslint-disable-next-line class-methods-use-this
  displayGallerySegment({ segment, segmentList }) {
    const segmentElem = document.createElement('div');
    segmentElem.classList.add('jinya-designer-item', 'jinya-designer-item--gallery');
    segmentElem.setAttribute('data-position', segment.position.toString(10));
    segmentElem.innerHTML = html`
        <span class="jinya-designer-item__title">
            ${localize({ key: 'pages_and_forms.segment.designer.gallery' })}
        </span>
        <span class="jinya-designer-item__details jinya-designer-item__details--gallery">
            ${segment.gallery.name}
        </span>`;
    segmentList.appendChild(segmentElem);
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
        <div class="cosmo-list">
            <nav class="cosmo-list__items" id="page-list">
            </nav>
            <div class="cosmo-list__content jinya-designer">
                <div class="jinya-designer__title">
                    <span class="cosmo-title" id="page-title"></span>
                </div>
                <div class="cosmo-toolbar cosmo-toolbar--designer">
                    <div class="cosmo-toolbar__group">
                        <button id="edit-page" class="cosmo-button">
                            ${localize({ key: 'pages_and_forms.segment.action.edit' })}
                        </button>
                        <button id="delete-page" class="cosmo-button">
                            ${localize({ key: 'pages_and_forms.segment.action.delete' })}
                        </button>
                    </div>
                    <div class="cosmo-toolbar__group">
                        <button id="edit-segment" class="cosmo-button" disabled>
                            ${localize({ key: 'pages_and_forms.segment.action.edit_segment' })}
                        </button>
                        <button id="delete-segment" class="cosmo-button" disabled>
                            ${localize({ key: 'pages_and_forms.segment.action.delete_segment' })}
                        </button>
                    </div>
                </div>
                <div class="jinya-designer__content">
                    <div id="segment-list" class="jinya-designer__result jinya-designer__result--horizontal">
                    </div>
                    <div id="segment-toolbox" class="jinya-designer__toolbox">
                        <div data-type="gallery" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'pages_and_forms.segment.designer.gallery' })}</span>
                        </div>
                        <div data-type="file" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'pages_and_forms.segment.designer.file' })}</span>
                        </div>
                        <div data-type="html" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'pages_and_forms.segment.designer.html' })}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
  }

  displayPages() {
    document.getElementById('edit-page').disabled = true;
    document.getElementById('delete-page').disabled = true;
    let list = '';
    for (const page of this.pages) {
      list += `<a class="cosmo-list__item" data-id="${page.id}">${page.name}</a>`;
    }
    clearChildren({ parent: document.getElementById('page-list') });
    document.getElementById('page-list').innerHTML = `${list}
                <button id="new-page-button" class="cosmo-button cosmo-button--full-width">
                    ${localize({ key: 'pages_and_forms.segment.action.new' })}
                </button>`;
    document.querySelectorAll('.cosmo-list__item').forEach((item) => {
      item.addEventListener('click', async () => {
        this.selectPage({ id: item.getAttribute('data-id') });
        await this.displaySelectedPage();
      });
    });
    document.getElementById('new-page-button').addEventListener('click', async () => {
      const { default: AddSegmentPageDialog } = await import('./segment-pages/AddSegmentPageDialog.js');
      const dialog = new AddSegmentPageDialog({
        onHide: async (page) => {
          this.pages.push(page);
          this.displayPages();
          this.selectPage({ id: page.id });
          await this.displaySelectedPage();
        },
      });
      dialog.show();
    });
  }

  async displayed() {
    await super.displayed();
    const { items } = await get('/api/segment-page');
    this.pages = items;

    this.toolboxSortable = new Sortable(document.getElementById('segment-toolbox'), {
      group: { name: 'segment_page', put: false, pull: 'clone' },
      sort: false,
      handle: '.jinya-designer__drag-handle',
      onEnd(e) {
        if (!e.to.classList.contains('jinya-designer__toolbox')) {
          e.item.remove();
        }
      },
    });

    this.displayPages();
    this.selectPage({ id: this.pages[0].id });
    await this.displaySelectedPage();
  }

  async openSegmentEditorUpdate() {
    if (this.selectedSegment.gallery) {
      const { default: EditGallerySegmentDialog } = await import('./segment-pages/EditGallerySegmentDialog.js');
      const dialog = new EditGallerySegmentDialog({
        id: this.selectedPage.id,
        galleryId: this.selectedSegment.gallery.id,
        position: this.selectedSegment.position,
        newSegment: false,
        onHide: async ({ gallery }) => {
          this.selectedSegment.gallery = gallery;
          this.segments.find((s) => s.position === this.selectedSegment.position).gallery = gallery;
          document.querySelector(`[data-position="${this.selectedSegment.position}"] .jinya-designer-item__details--gallery`).innerText = gallery.name;
        },
      });
      await dialog.show();
    } else if (this.selectedSegment.file) {
      const { default: EditFileSegmentDialog } = await import('./segment-pages/EditFileSegmentDialog.js');
      const dialog = new EditFileSegmentDialog({
        id: this.selectedPage.id,
        fileId: this.selectedSegment.file.id,
        target: this.selectedSegment.target,
        position: this.selectedSegment.position,
        newSegment: false,
        onHide: async ({ file, target }) => {
          this.selectedSegment.file = file;
          this.selectedSegment.target = target;
          this.segments.find((s) => s.position === this.selectedSegment.position).file = file;
          this.segments.find((s) => s.position === this.selectedSegment.position).target = target;
          let action = '';
          if (!target) {
            document
              .querySelectorAll(`[data-position="${this.selectedSegment.position}"] [data-type="target"]`)
              .forEach((item) => item.setAttribute('style', 'display: none;'));
            action = 'none';
          } else {
            action = 'link';
            document
              .querySelectorAll(`[data-position="${this.selectedSegment.position}"] [data-type="target"]`)
              .forEach((item) => item.removeAttribute('style'));
            document
              .querySelector(`[data-position="${this.selectedSegment.position}"] [data-action="target"]`)
              .innerText = target;
            document
              .querySelector(`[data-position="${this.selectedSegment.position}"] .jinya-segment__image`)
              .src = file.path;
          }
          document
            .querySelector(`[data-position="${this.selectedSegment.position}"] [data-type="action-label"]`)
            .innerText = localize({ key: `pages_and_forms.segment.designer.action_${action}` });
        },
      });
      await dialog.show();
    } else if (this.selectedSegment.html) {
      const { default: EditHtmlSegmentDialog } = await import('./segment-pages/EditHtmlSegmentDialog.js');
      const dialog = new EditHtmlSegmentDialog({
        id: this.selectedPage.id,
        html: this.selectedSegment.html,
        position: this.selectedSegment.position,
        newSegment: false,
        onHide: async ({ html: content }) => {
          this.selectedSegment.html = content;
          this.segments.find((s) => s.position === this.selectedSegment.position).html = content;
          document
            .querySelector(`[data-position="${this.selectedSegment.position}"] .jinya-designer-item__details--html`)
            .innerHTML = content;
        },
      });
      await dialog.show();
    }
  }

  async openSegmentEditorCreate({ position, type }) {
    if (type === 'gallery') {
      const { default: EditGallerySegmentDialog } = await import('./segment-pages/EditGallerySegmentDialog.js');
      const dialog = new EditGallerySegmentDialog({
        id: this.selectedPage.id,
        galleryId: -1,
        position,
        newSegment: true,
        onHide: ({ segment }) => {
          this.segments.splice(segment.position, 0, segment);
          this.displaySegments();
        },
      });
      await dialog.show();
    } else if (type === 'file') {
      const { default: EditFileSegmentDialog } = await import('./segment-pages/EditFileSegmentDialog.js');
      const dialog = new EditFileSegmentDialog({
        id: this.selectedPage.id,
        fileId: -1,
        target: '',
        position,
        newSegment: true,
        onHide: ({ segment }) => {
          this.segments.splice(segment.position, 0, segment);
          this.displaySegments();
        },
      });
      await dialog.show();
    } else if (type === 'html') {
      const { default: EditHtmlSegmentDialog } = await import('./segment-pages/EditHtmlSegmentDialog.js');
      const dialog = new EditHtmlSegmentDialog({
        id: this.selectedPage.id,
        html: '',
        position,
        newSegment: true,
        onHide: ({ segment }) => {
          this.segments.splice(segment.position, 0, segment);
          this.displaySegments();
        },
      });
      await dialog.show();
    }
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('edit-page').addEventListener('click', async () => {
      const { default: EditSegmentPageDialog } = await import('./segment-pages/EditSegmentPageDialog.js');
      const dialog = new EditSegmentPageDialog({
        onHide: async ({ id, name }) => {
          this.pages.find((p) => p.id === id).name = name;
          this.displayPages();
          this.selectPage({ id });
          await this.displaySelectedPage();
        },
        name: this.selectedPage.name,
        id: this.selectedPage.id,
      });
      dialog.show();
    });
    document.getElementById('delete-page').addEventListener('click', async () => {
      const confirmation = await confirm({
        title: localize({ key: 'pages_and_forms.segment.delete.title' }),
        message: localize({ key: 'pages_and_forms.segment.delete.message', values: this.selectedPage }),
        approveLabel: localize({ key: 'pages_and_forms.segment.delete.delete' }),
        declineLabel: localize({ key: 'pages_and_forms.segment.delete.keep' }),
      });
      if (confirmation) {
        try {
          await httpDelete(`/api/segment-page/${this.selectedPage.id}`);
          this.pages = this.pages.filter((page) => page.id !== this.selectedPage.id);
          this.displayPages();
          if (this.pages.length > 0) {
            this.selectPage({ id: this.pages[0].id });
          } else {
            this.selectedPage = null;
            this.segments = [];
            await this.displaySegments();
            document.getElementById('page-title').innerText = '';
          }
          document.getElementById('edit-segment').setAttribute('disabled', 'disabled');
          document.getElementById('delete-segment').setAttribute('disabled', 'disabled');
        } catch (e) {
          if (e.status === 409) {
            await alert({
              title: localize({ key: 'pages_and_forms.segment.delete.error.title' }),
              message: localize({ key: 'pages_and_forms.segment.delete.error.conflict' }),
            });
          } else {
            await alert({
              title: localize({ key: 'pages_and_forms.segment.delete.error.title' }),
              message: localize({ key: 'pages_and_forms.segment.delete.error.generic' }),
            });
          }
        }
      }
    });
    document.getElementById('edit-segment').addEventListener('click', async () => {
      await this.openSegmentEditorUpdate();
    });
    document.getElementById('delete-segment').addEventListener('click', async () => {
      const confirmation = await confirm({
        title: localize({ key: 'pages_and_forms.segment.delete_segment.title' }),
        message: localize({ key: 'pages_and_forms.segment.delete_segment.message', values: this.selectedPage }),
        approveLabel: localize({ key: 'pages_and_forms.segment.delete_segment.delete' }),
        declineLabel: localize({ key: 'pages_and_forms.segment.delete_segment.keep' }),
      });
      if (confirmation) {
        const { position } = this.selectedSegment;
        const segmentElem = document.querySelector(`[data-position="${position}"]`);
        segmentElem.remove();
        this.resetPositions();
        this.selectedSegment = null;
        document.getElementById('edit-segment').setAttribute('disabled', 'disabled');
        document.getElementById('delete-segment').setAttribute('disabled', 'disabled');
        await httpDelete(`/api/segment-page/${this.selectedPage.id}/segment/${position}`);
      }
    });
  }
}
