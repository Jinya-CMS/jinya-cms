/**
 * Displays a confirm modal dialog
 * @param title {string}
 * @param message {string}
 * @param declineLabel {string}
 * @param approveLabel {string}
 * @param negative {boolean}
 * @return {Promise<boolean>}
 */
export default async function confirm({
  title = window.location.href,
  message,
  declineLabel,
  approveLabel,
  negative = false,
}) {
  return new Promise((resolve) => {
    const container = document.createElement('div');
    document.body.appendChild(container);
    const modalId = crypto.randomUUID();

    container.innerHTML = `
      <div class="cosmo-modal__container">
        <div class="cosmo-modal ${negative ? 'is--negative' : ''}">
          <h1 class="cosmo-modal__title">${title}</h1>
          <p class="cosmo-modal__content">${message}</p>
          <div class="cosmo-modal__button-bar">
            <button id="${modalId}DeclineButton" class="cosmo-button">${declineLabel}</button>
            <button id="${modalId}ApproveButton" class="cosmo-button">${approveLabel}</button>
          </div>
        </div>
      </div>`;

    document.body.appendChild(container);

    document.getElementById(`${modalId}DeclineButton`).addEventListener('click', (e) => {
      e.preventDefault();
      container.remove();
      resolve(false);
    });
    document.getElementById(`${modalId}ApproveButton`).addEventListener('click', (e) => {
      e.preventDefault();
      container.remove();
      resolve(true);
    });
  });
}
