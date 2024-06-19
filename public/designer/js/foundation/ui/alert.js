import localize from '../utils/localize.js';

/**
 * Displays an alert modal dialog
 * @param title {string}
 * @param message {string}
 * @param buttonLabel {string}
 * @param negative {boolean}
 * @param positive {boolean}
 * @return {Promise<void>}
 */
export default async function alert({
  title = window.location.href,
  message,
  buttonLabel = null,
  negative = false,
  positive = false,
}) {
  if (buttonLabel === null) {
    buttonLabel = localize({ key: 'alert.dismiss' });
  }
  return new Promise((resolve) => {
    const container = document.createElement('div');
    document.body.appendChild(container);
    const modalId = crypto.randomUUID();

    container.innerHTML = `
      <div class="cosmo-modal__container">
        <div class="cosmo-modal ${negative ? 'is--negative' : ''} ${positive ? 'is--positive' : ''}">
          <h1 class="cosmo-modal__title">${title}</h1>
          <p class="cosmo-modal__content">${message}</p>
          <div class="cosmo-modal__button-bar">
            <button id="${modalId}DismissButton" class="cosmo-button">${buttonLabel}</button>
          </div>
        </div>
      </div>`;

    document.body.appendChild(container);

    document.getElementById(`${modalId}DismissButton`).addEventListener('click', (e) => {
      e.preventDefault();
      container.remove();
      resolve();
    });
  });
}
