<script>
  export let title;
  export let message;
  export let approveLabel = 'Approve';
  export let declineLabel = 'Decline';
  export let onApproveClick = () => {};
  export let onDeclineClick = () => {};
</script>

<div class="jinya-modal__backdrop"></div>
<div class="jinya-modal__container">
    <div class="jinya-modal">
        <h1 class="jinya-modal__title">{title}</h1>
        <p class="jinya-modal__content">{@html message}</p>
        <div class="jinya-modal__button-bar">
            <button class="jinya-button" on:click={onDeclineClick}>{declineLabel}</button>
            <button class="jinya-button" on:click={onApproveClick}>{approveLabel}</button>
        </div>
    </div>
</div>
