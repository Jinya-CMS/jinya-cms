import localize from '../utils/localize.js';
import { getFiles, getTags } from '../api/files.js';

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
  const { items: files } = await getFiles();
  const { items: tags } = await getTags();

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

    container.innerHTML = `
      <div class="cosmo-modal__container is--file-picker">
        <div class="cosmo-modal is--file-picker">
          <h1 class="cosmo-modal__title">${title}</h1>
          <div class="cosmo-modal__content">
            <div class="jinya-picker__tag-list">
              <cms-tag
                class="jinya-tag is--file"
                emoji=""
                name="${localize({ key: 'media.galleries.action.show_all_tags' })}"
                color="#19324c"
                tag-id="-1"
                id="show-all-tags"
              ></cms-tag>
              ${tags.map((tag) => `
                <cms-tag
                  class="jinya-tag is--file"
                  emoji="${tag.emoji}"
                  name="${tag.name}"
                  color="${tag.color}"
                  tag-id="${tag.id}"
                  id="show-tag-${tag.id}"
                ></cms-tag>`)
      .join('')}
            </div>
            <div class="jinya-media-tile__container is--modal">
              ${files.map((file) => `
                <div
                  class="jinya-media-tile is--small ${selectedFileId === file.id
      ? 'jinya-media-tile--selected'
      : ''}"
                  data-id="${file.id}"
                >
                  <img
                    class="jinya-media-tile__img is--small"
                    data-id="${file.id}"
                    src="${file.path}"
                    alt="${file.name}"
                  />
                </div>`)
      .join('')}
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button id="${modalId}CancelButton" class="cosmo-button">${cancelLabel}</button>
            <button id="${modalId}PickButton" class="cosmo-button">${pickLabel}</button>
          </div>
        </div>
      </div>`;

    document.body.appendChild(container);

    document.querySelectorAll('.jinya-picker__tag-list cms-tag')
      .forEach((tag) => tag.addEventListener('click', (evt) => {
        evt.stopPropagation();
        // eslint-disable-next-line no-param-reassign
        tag.active = !tag.active;
        if (tag.id === 'show-all-tags') {
          container
            .querySelectorAll('.jinya-media-tile')
            .forEach((tile) => tile.classList.remove('is--hidden'));
          container.querySelectorAll('cms-tag')
            .forEach((t) => {
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
          container.querySelectorAll('.jinya-media-tile')
            .forEach((tile) => {
              const file = files.find((f) => f.id === parseInt(tile.getAttribute('data-id'), 10));
              if (file.tags.filter((f) => activeTags.has(f.id)).length === 0) {
                tile.classList.add('is--hidden');
              } else {
                tile.classList.remove('is--hidden');
              }
            });

          if (allTags.active) {
            activeTags.clear();
            container.querySelectorAll('.jinya-media-tile')
              .forEach((tile) => {
                tile.classList.remove('is--hidden');
              });
          }
        }
      }));
    container.querySelectorAll('.jinya-media-tile')
      .forEach((item) => {
        item.addEventListener('click', (e) => {
          e.preventDefault();
          container
            .querySelectorAll('.is--selected')
            .forEach((tile) => tile.classList.remove('is--selected'));
          item.classList.add('is--selected');
        });
      });
    document.getElementById(`${modalId}CancelButton`)
      .addEventListener('click', (e) => {
        e.preventDefault();
        container.remove();
        resolve(null);
      });
    document.getElementById(`${modalId}PickButton`)
      .addEventListener('click', (e) => {
        e.preventDefault();
        const selectedFile = files.find(
          (file) => parseInt(document.querySelector('.is--selected')
            .getAttribute('data-id'), 10) === file.id,
        );
        container.remove();
        resolve(selectedFile);
      });
  });
}
