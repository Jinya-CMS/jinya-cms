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

<style scoped lang="scss">
    .jinya-editor-pane {
        width: 50%;
        padding: 0.5em;
        display: flex;
        flex-wrap: wrap;
        align-content: start;

        &:nth-child(1) {
            padding-left: 0;
        }

        &:nth-child(2) {
            padding-right: 0;
        }
    }
</style>
