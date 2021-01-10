import AlertModal from './AlertModal.svelte';

/**
 *
 * @param title string
 * @param message string
 * @param buttonLabel string
 * @returns {Promise<void>}
 */
export function jinyaAlert(title, message, buttonLabel = 'Dismiss') {
  return new Promise((resolve => {
    const container = document.createElement('div');
    container.id = 'jinya-modal-container';
    document.body.appendChild(container);
    new AlertModal({
      target: container,
      props: {
        title,
        message,
        buttonLabel,
        onButtonClick() {
          container.remove();
          resolve();
        },
      }
    });
  }));
}
