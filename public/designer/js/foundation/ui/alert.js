import html from '../../../lib/jinya-html.js';
import localize from '../localize.js';

/**
 * Displays an alert modal dialog
 * @param title {string}
 * @param message {string}
 * @param buttonLabel {string}
 * @return {Promise<void>}
 */
export default async function alert({
                                      title = window.location.href,
                                      message,
                                      buttonLabel = null,
                                    }) {
  if (buttonLabel === null) {
    // eslint-disable-next-line no-param-reassign
    buttonLabel = localize({ key: 'alert.dismiss' });
  }
  return new Promise((resolve) => {
    const container = document.createElement('div');
    document.body.appendChild(container);
    const modalId = crypto.randomUUID();

    container.innerHTML = html` <div class="cosmo-modal__backdrop"></div>
      <div class="cosmo-modal__container">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${title}</h1>
          <p class="cosmo-modal__content">${message}</p>
          <div class="cosmo-modal__button-bar">
            <button id="${modalId}DismissButton" class="cosmo-button">${buttonLabel}</button>
          </div>
        </div>
      </div>`;

    document.body.appendChild(container);

    document.getElementById(`${modalId}DismissButton`)
      .addEventListener('click', (e) => {
        e.preventDefault();
        container.remove();
        resolve();
      });
  });
}
