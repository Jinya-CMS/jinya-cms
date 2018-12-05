<template>
  <div class="jinya-message__action-bar">
    <slot/>
  </div>
</template>

<script>
  export default {
    name: 'jinya-message-action-bar',
  };
</script>

<style scoped lang="scss">
  .jinya-message__action-bar {
    display: block;
    padding-top: 1em;

    .jinya-button {
      margin: 0 0.5em;

      &:first-child {
        margin-left: 0;
      }
      &:last-child {
        margin-right: 0;
      }
    }
  }
</style>
