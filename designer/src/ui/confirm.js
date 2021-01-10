import ConfirmModal from './ConfirmModal.svelte';

/**
 *
 * @param title string
 * @param message string
 * @param approveLabel string
 * @param declineLabel string
 * @returns {Promise<boolean>}
 */
export function jinyaConfirm(title, message, approveLabel = 'Approve', declineLabel = 'Decline') {
  return new Promise((resolve => {
    const container = document.createElement('div');
    container.id = 'jinya-modal-container';
    document.body.appendChild(container);
    new ConfirmModal({
      target: container,
      props: {
        title,
        message,
        approveLabel,
        declineLabel,
        onApproveClick() {
          container.remove();
          resolve(true);
        },
        onDeclineClick() {
          container.remove();
          resolve(false);
        },
      }
    });
  }));
}
