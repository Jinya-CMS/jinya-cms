<script>
  export let title;
  export let message;
  export let buttonLabel = 'Acknowledge';
  export let onButtonClick = () => {};
</script>

<div class="jinya-modal__backdrop"></div>
<div class="jinya-modal__container">
    <div class="jinya-modal">
        <div class="jinya-modal__header">
            <div class="jinya-modal__deco-bar"></div>
        </div>
        <div class="jinya-modal__body">
            <h1 class="jinya-modal__title">{title}</h1>
            <p class="jinya-modal__content">{@html message}</p>
            <div class="jinya-modal__button-bar">
                <button class="jinya-button" on:click={onButtonClick}>{buttonLabel}</button>
            </div>
        </div>
    </div>
</div>
