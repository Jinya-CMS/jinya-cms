<template>
  <div class="jinya-gallery-designer__item">
    <slot/>
  </div>
</template>

<script>
  export default {
    name: 'jinya-gallery-designer-item',
  };
</script>

<style lang="scss">
  .jinya-gallery-designer__item {
    display: grid;

    .is--vertical & {
      grid-template-rows: auto 1fr auto;
    }
  }
</style>
