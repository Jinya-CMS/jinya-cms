<template>
  <div class="jinya-editor">
    <slot/>
  </div>
</template>

<script>
  export default {
    name: 'jinya-editor',
  };
</script>

<style lang="scss" scoped>
  .jinya-editor {
    padding-top: 1em;
    display: flex;
  }
</style>
