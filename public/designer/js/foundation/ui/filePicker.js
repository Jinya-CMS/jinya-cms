import html from '../../../lib/jinya-html.js';
import { get } from '../http/request.js';
import localize from '../localize.js';

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

  return new Promise((resolve) => {
    const container = document.createElement('div');
    document.body.appendChild(container);
    const modalId = crypto.randomUUID();

    if (!cancelLabel) {
      // eslint-disable-next-line no-param-reassign
      cancelLabel = localize({ key: 'file_picker.dismiss' });
      // eslint-disable-next-line no-param-reassign
      pickLabel = localize({ key: 'file_picker.pick' });
    }

    container.innerHTML = html`
        <div class="cosmo-modal__backdrop"></div>
        <div class="cosmo-modal__container">
            <div class="cosmo-modal jinya-file-picker__modal">
                <h1 class="cosmo-modal__title">${title}</h1>
                <div class="cosmo-modal__content">
                    <div class="jinya-media-tile__container--modal">
                        ${files.map((file) => html`
                            <div class="jinya-media-tile jinya-media-tile--medium ${selectedFileId === file.id ? 'jinya-media-tile--selected' : ''}"
                                 data-id="${file.id}">
                                <img class="jinya-media-tile__img jinya-media-tile__img--small" data-id="${file.id}"
                                     src="${file.path}" alt="${file.name}">
                            </div>`)}
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button id="${modalId}CancelButton" class="cosmo-button">${cancelLabel}</button>
                    <button id="${modalId}PickButton" class="cosmo-button">${pickLabel}</button>
                </div>
            </div>
        </div>`;

    document.body.appendChild(container);

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
      const selectedFile = files
        .find((file) => parseInt(document
          .querySelector('.jinya-media-tile--selected')
          .getAttribute('data-id'), 10) === file.id);
      container.remove();
      resolve(selectedFile);
    });
  });
}
