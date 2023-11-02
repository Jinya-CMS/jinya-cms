import html from '../../../lib/jinya-html.js';
import { get } from '../http/request.js';
import localize from '../localize.js';
import './components/tag.js';

/**
 * Displays a file picker modal dialog
 * @param title {string}
 * @param message {string}
 * @param cancelLabel {string}
 * @param pickLabel {string}
 * @return {Promise<boolean>}
 */
export default async function filePicker({
  title = window.location.href,
  selectedFileId = -1,
  cancelLabel,
  pickLabel,
}) {
  const { items: files } = await get('/api/file');
  const { items: tags } = await get('/api/file-tag');

  return new Promise((resolve) => {
    const container = document.createElement('div');
    document.body.appendChild(container);
    const modalId = crypto.randomUUID();
    const activeTags = new Set();

    if (!cancelLabel) {
      // eslint-disable-next-line no-param-reassign
      cancelLabel = localize({ key: 'file_picker.dismiss' });
      // eslint-disable-next-line no-param-reassign
      pickLabel = localize({ key: 'file_picker.pick' });
    }

    container.innerHTML = html` <div class="cosmo-modal__backdrop"></div>
      <div class="cosmo-modal__container">
        <div class="cosmo-modal jinya-file-picker__modal">
          <h1 class="cosmo-modal__title">${title}</h1>
          <div class="cosmo-modal__content">
            <div class="jinya-picker__tag-list">
              <cms-tag
                class="jinya-tag--file"
                emoji=""
                name="${localize({ key: 'media.galleries.action.show_all_tags' })}"
                color="#19324c"
                tag-id="-1"
                id="show-all-tags"
              ></cms-tag>
              ${tags.map(
                (tag) =>
                  html` <cms-tag
                    class="jinya-tag--file"
                    emoji="${tag.emoji}"
                    name="${tag.name}"
                    color="${tag.color}"
                    tag-id="${tag.id}"
                    id="show-tag-${tag.id}"
                  ></cms-tag>`,
              )}
            </div>
            <div class="jinya-media-tile__container--modal">
              ${files.map(
                (file) =>
                  html` <div
                    class="jinya-media-tile jinya-media-tile--medium ${selectedFileId === file.id
                      ? 'jinya-media-tile--selected'
                      : ''}"
                    data-id="${file.id}"
                  >
                    <img
                      class="jinya-media-tile__img jinya-media-tile__img--small"
                      data-id="${file.id}"
                      src="${file.path}"
                      alt="${file.name}"
                    />
                  </div>`,
              )}
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button id="${modalId}CancelButton" class="cosmo-button">${cancelLabel}</button>
            <button id="${modalId}PickButton" class="cosmo-button">${pickLabel}</button>
          </div>
        </div>
      </div>`;

    document.body.appendChild(container);

    document.querySelectorAll('.jinya-picker__tag-list cms-tag').forEach((tag) =>
      tag.addEventListener('click', (evt) => {
        evt.stopPropagation();
        // eslint-disable-next-line no-param-reassign
        tag.active = !tag.active;
        if (tag.id === 'show-all-tags') {
          container
            .querySelectorAll('.jinya-media-tile')
            .forEach((tile) => tile.classList.remove('jinya-media-tile--hidden'));
          container.querySelectorAll('cms-tag').forEach((t) => {
            // eslint-disable-next-line no-param-reassign
            t.active = false;
          });
          // eslint-disable-next-line no-param-reassign
          tag.active = true;
        } else {
          const allTags = container.querySelector('#show-all-tags');
          if (tag.active) {
            activeTags.add(tag.tagId);
          } else {
            activeTags.delete(tag.tagId);
          }
          allTags.active = activeTags.size === 0 || activeTags.size === tags.length;
          container.querySelectorAll('.jinya-media-tile').forEach((tile) => {
            const file = files.find((f) => f.id === parseInt(tile.getAttribute('data-id'), 10));
            if (file.tags.filter((f) => activeTags.has(f.id)).length === 0) {
              tile.classList.add('jinya-media-tile--hidden');
            } else {
              tile.classList.remove('jinya-media-tile--hidden');
            }
          });

          if (allTags.active) {
            activeTags.clear();
            container.querySelectorAll('.jinya-media-tile').forEach((tile) => {
              tile.classList.remove('jinya-media-tile--hidden');
            });
          }
        }
      }),
    );
    container.querySelectorAll('.jinya-media-tile').forEach((item) => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        container
          .querySelectorAll('.jinya-media-tile--selected')
          .forEach((tile) => tile.classList.remove('jinya-media-tile--selected'));
        item.classList.add('jinya-media-tile--selected');
      });
    });
    document.getElementById(`${modalId}CancelButton`).addEventListener('click', (e) => {
      e.preventDefault();
      container.remove();
      resolve(null);
    });
    document.getElementById(`${modalId}PickButton`).addEventListener('click', (e) => {
      e.preventDefault();
      const selectedFile = files.find(
        (file) =>
          parseInt(document.querySelector('.jinya-media-tile--selected').getAttribute('data-id'), 10) === file.id,
      );
      container.remove();
      resolve(selectedFile);
    });
  });
}
