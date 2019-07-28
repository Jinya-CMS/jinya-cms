<template>
  <div class="jinya-editor-pane">
    <slot/>
  </div>
</template>

<script>
  export default {
    name: 'jinya-editor-pane',
  };
</script>

<style lang="scss" scoped>
  .jinya-editor-pane {
    flex: 0 0 50%;
    width: 50%;
    padding: 0.5em;
    display: flex;
    flex-wrap: wrap;
    align-content: start;
    box-sizing: border-box;
  }
</style>
