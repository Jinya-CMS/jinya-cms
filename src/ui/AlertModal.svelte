<script>
  export let title;
  export let message;
  export let buttonLabel = 'Acknowledge';
  export let onButtonClick = () => {};
</script>

<div class="cosmo-modal__backdrop"></div>
<div class="cosmo-modal__container">
    <div class="cosmo-modal">
        <h1 class="cosmo-modal__title">{title}</h1>
        <p class="cosmo-modal__content">{@html message}</p>
        <div class="cosmo-modal__button-bar">
            <button class="cosmo-button" on:click={onButtonClick}>{buttonLabel}</button>
        </div>
    </div>
</div>
