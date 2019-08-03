<template>
    <div class="jinya-page-editor__preview-html" v-html="html"></div>
</template>

<script>
  export default {
    name: 'JinyaPageEditorPreviewElementHtml',
    props: {
      html: {
        type: String,
        required: true,
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page-editor__preview-html {
        width: 100%;
    }
</style>
