import html from '../../../../lib/jinya-html.js';
import Sortable from '../../../../lib/sortable.js';
import clearChildren from '../../../foundation/html/clearChildren.js';
import { put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import confirm from '../../../foundation/ui/confirm.js';

export default class PostDesignerDialog {
  constructor({
                post,
                segments,
              }) {
    this.post = post;
    this.selectedSegment = {};
    this.segments = segments;
    this.resultSortable = null;
    this.toolboxSortable = null;
    this.newSegment = false;
  }

  // eslint-disable-next-line class-methods-use-this
  resetPositions() {
    document
      .getElementById('segment-list')
      .querySelectorAll('[data-position]')
      .forEach((elem, key) => {
        // eslint-disable-next-line no-param-reassign
        elem.segment.position = key;
        this.segments.find((s) => s === elem.segment).position = key;
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
        .classList
        .add('jinya-designer-item__details--file-selected');
      item.querySelector('.jinya-segment__image')
        .classList
        .add('jinya-segment__image--file-selected');
    }

    document.getElementById('edit-segment')
      .removeAttribute('disabled');
    document.getElementById('delete-segment')
      .removeAttribute('disabled');
    this.selectedSegment = this.segments.find((s) => s.position === parseInt(segmentPosition, 10));
  }

  displaySegments() {
    if (this.resultSortable) {
      this.resultSortable.destroy();
    }
    const segmentList = document.getElementById('segment-list');
    clearChildren({ parent: segmentList });
    for (const segment of this.segments) {
      if (segment.html) {
        this.displayHtmlSegment({
          segment,
          segmentList,
        });
      } else if (segment.file) {
        this.displayFileSegment({
          segment,
          segmentList,
        });
      } else if (segment.gallery) {
        this.displayGallerySegment({
          segment,
          segmentList,
        });
      }
    }
    document.querySelectorAll('#segment-list .jinya-designer-item')
      .forEach((item) => {
        item.addEventListener('click', () => {
          this.selectSegment({ segmentPosition: item.getAttribute('data-position') });
        });
      });
    this.resultSortable = new Sortable(document.getElementById('segment-list'), {
      group: {
        name: 'post',
        put: true,
        pull: false,
      },
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
        this.newSegment = true;
        await this.openSegmentEditor({
          position: createPosition,
          type: createNewSegmentType,
        });
      },
      onUpdate: () => {
        this.resetPositions();
      },
    });
  }

  /**
   * @param segment {{id: number, position: number, html: string}}
   * @param segmentList {HTMLDivElement}
   */
  // eslint-disable-next-line class-methods-use-this
  displayHtmlSegment({
                       segment,
                       segmentList,
                     }) {
    const segmentElem = document.createElement('div');
    segmentElem.classList.add('jinya-designer-item', 'jinya-designer-item--html');
    segmentElem.setAttribute('data-position', segment.position.toString(10));
    segmentElem.innerHTML = html`
      <span class="jinya-designer-item__title">
        ${localize({ key: 'pages_and_forms.segment.designer.html' })}
      </span>
      <div class="jinya-designer-item__details jinya-designer-item__details--html">${segment.html}</div>`;
    segmentElem.segment = segment;
    segmentList.appendChild(segmentElem);
  }

  /**
   * @param segment {{id: number, position: number, action: string, link: string, file: {name: string, path: string}}}
   * @param segmentList {HTMLDivElement}
   */
  // eslint-disable-next-line class-methods-use-this
  displayFileSegment({
                       segment,
                       segmentList,
                     }) {
    const segmentElem = document.createElement('div');
    segmentElem.classList.add('jinya-designer-item', 'jinya-designer-item--file');
    segmentElem.setAttribute('data-position', segment.position.toString(10));
    segmentElem.setAttribute('data-is-file', 'true');
    segmentElem.innerHTML = html` <img
      class="jinya-segment__image"
      src="${segment.file.path}"
      alt="${segment.file.name}"
    />
    <div class="jinya-designer-item__details jinya-designer-item__details--file">
      <span class="jinya-designer-item__title"> ${localize({ key: 'pages_and_forms.segment.designer.file' })} </span>
      <dl class="jinya-segment__action">
        <dt class="jinya-segment__label">${localize({ key: 'blog.posts.designer.name' })}</dt>
        <dd class="jinya-segment__content" data-type="action-label">${segment.file.name}</dd>
        <dt class="jinya-segment__label" data-type="link" ${segment.link === '' ? 'style="display: none;"' : ''}>
          ${localize({ key: 'blog.posts.designer.link' })}
        </dt>
        <dd
          class="jinya-segment__content"
          data-type="link"
          data-action="link"
          ${segment.link === '' ? 'style="display: none;"' : ''}
        >
          ${segment.link}
        </dd>
      </dl>
    </div>`;
    segmentElem.segment = segment;
    segmentList.appendChild(segmentElem);
  }

  /**
   * @param segment {{id: number, position: number, gallery: {name: string}}}
   * @param segmentList {HTMLDivElement}
   */
  // eslint-disable-next-line class-methods-use-this
  displayGallerySegment({
                          segment,
                          segmentList,
                        }) {
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
    segmentElem.segment = segment;
    segmentList.appendChild(segmentElem);
  }

  async openSegmentEditor({
                            position,
                            type,
                          }) {
    if (type === 'gallery') {
      const { default: EditGallerySegmentDialog } = await import('./EditGallerySegmentDialog.js');
      const dialog = new EditGallerySegmentDialog({
        position,
        ...this.selectedSegment,
        galleryId: this.selectedSegment?.gallery?.id ?? -1,
        onHide: ({ gallery }) => {
          if (this.newSegment) {
            this.segments.splice(position, 0, {
              position,
              gallery,
            });
          } else {
            this.selectedSegment.gallery = gallery;
            this.segments.find((s) => s.position === this.selectedSegment.position).gallery = gallery;
          }
          this.displaySegments();
        },
      });
      await dialog.show();
    } else if (type === 'file') {
      const { default: EditFileSegmentDialog } = await import('./EditFileSegmentDialog.js');
      const dialog = new EditFileSegmentDialog({
        ...this.selectedSegment,
        fileId: this.selectedSegment?.file?.id ?? -1,
        onHide: ({
                   file,
                   link,
                 }) => {
          if (this.newSegment) {
            this.segments.splice(position, 0, {
              position,
              file,
              link,
            });
          } else {
            this.selectedSegment.file = file;
            this.selectedSegment.link = link;
            this.segments.find((s) => s.position === this.selectedSegment.position).file = file;
            this.segments.find((s) => s.position === this.selectedSegment.position).link = link;
          }
          this.displaySegments();
        },
        position,
      });
      await dialog.show();
    } else if (type === 'html') {
      const { default: EditHtmlSegmentDialog } = await import('./EditHtmlSegmentDialog.js');
      const dialog = new EditHtmlSegmentDialog({
        ...this.selectedSegment,
        position,
        onHide: ({ html: newHtml }) => {
          if (this.newSegment) {
            this.segments.splice(position, 0, {
              position,
              html: newHtml,
            });
          } else {
            this.selectedSegment.html = newHtml;
            this.segments.find((s) => s.position === this.selectedSegment.position).html = newHtml;
          }
          this.displaySegments();
        },
      });
      await dialog.show();
    }
  }

  show() {
    const content = html`
      <form class="cosmo-modal__container" id="post-designer-dialog">
        <div class="cosmo-modal jinya-designer__modal--blog">
          <h1 class="cosmo-modal__title">${localize({ key: 'blog.posts.designer.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-toolbar cosmo-toolbar--blog">
              <div class="cosmo-toolbar__group">
                <button type="button" id="edit-segment" class="cosmo-button" disabled>
                  ${localize({ key: 'blog.posts.designer.action.edit_segment' })}
                </button>
                <button type="button" id="delete-segment" class="cosmo-button" disabled>
                  ${localize({ key: 'blog.posts.designer.action.delete_segment' })}
                </button>
              </div>
            </div>
            <div class="jinya-designer__content jinya-designer__content--blog">
              <div
                id="segment-list"
                class="jinya-designer__result jinya-designer__result--blog jinya-designer__result--horizontal"
              ></div>
              <div id="segment-toolbox" class="jinya-designer__toolbox">
                <div data-type="gallery" class="jinya-designer-item__template">
                  <span class="jinya-designer__drag-handle"></span>
                  <span>${localize({ key: 'blog.posts.designer.gallery' })}</span>
                </div>
                <div data-type="file" class="jinya-designer-item__template">
                  <span class="jinya-designer__drag-handle"></span>
                  <span>${localize({ key: 'blog.posts.designer.file' })}</span>
                </div>
                <div data-type="html" class="jinya-designer-item__template">
                  <span class="jinya-designer__drag-handle"></span>
                  <span>${localize({ key: 'blog.posts.designer.html' })}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button type="button" class="cosmo-button" id="cancel-post-dialog">
              ${localize({ key: 'blog.posts.designer.cancel' })}
            </button>
            <button type="submit" class="cosmo-button" id="close-dialog">
              ${localize({ key: 'blog.posts.designer.save' })}
            </button>
          </div>
        </div>
      </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    this.displaySegments();

    this.toolboxSortable = new Sortable(document.getElementById('segment-toolbox'), {
      group: {
        name: 'post',
        put: false,
        pull: 'clone',
      },
      sort: false,
      handle: '.jinya-designer__drag-handle',
      onEnd(e) {
        if (!e.to.classList.contains('jinya-designer__toolbox')) {
          e.item.remove();
        }
      },
    });

    document.getElementById('cancel-post-dialog')
      .addEventListener('click', () => {
        this.toolboxSortable.destroy();
        this.resultSortable.destroy();
        container.remove();
      });
    document.getElementById('post-designer-dialog')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        await put(`/api/blog/post/${this.post.id}/segment`, {
          segments: this.segments
            .map((segment) => {
              const data = {
                position: segment.position,
              };
              if (segment.file) {
                data.file = segment.file.id;
                data.link = segment.link;
              } else if (segment.gallery) {
                data.gallery = segment.gallery.id;
              } else if (segment.html) {
                data.html = segment.html;
              }

              return data;
            })
            .sort((a, b) => a.position - b.position),
        });
        container.remove();
      });
    document.getElementById('edit-segment')
      .addEventListener('click', () => {
        let type = '';
        if (this.selectedSegment.file) {
          type = 'file';
        } else if (this.selectedSegment.gallery) {
          type = 'gallery';
        } else if (this.selectedSegment.html) {
          type = 'html';
        }
        this.newSegment = false;
        this.openSegmentEditor({
          position: this.selectedSegment.position,
          type,
        });
      });
    document.getElementById('delete-segment')
      .addEventListener('click', async () => {
        const confirmation = await confirm({
          title: localize({ key: 'blog.posts.designer.edit.delete_segment.title' }),
          message: localize({
            key: 'blog.posts.designer.edit.delete_segment.message',
            values: this.selectedPage,
          }),
          approveLabel: localize({ key: 'blog.posts.designer.edit.delete_segment.delete' }),
          declineLabel: localize({ key: 'blog.posts.designer.edit.delete_segment.keep' }),
          negative: true,
        });
        if (confirmation) {
          const { position } = this.selectedSegment;
          const segmentElem = document.querySelector(`[data-position="${position}"]`);
          segmentElem.remove();
          this.segments.splice(position, 1);
          this.resetPositions();
          this.selectedSegment = null;
          document.getElementById('edit-segment')
            .setAttribute('disabled', 'disabled');
          document.getElementById('delete-segment')
            .setAttribute('disabled', 'disabled');
        }
      });
  }
}
