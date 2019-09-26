<template>
    <figure class="jinya-page-editor__preview-figure">
        <figcaption class="jinya-page-editor__preview-caption">{{file.name}}</figcaption>
        <img :alt="file.name" :src="file.path" class="jinya-page-editor__preview-image"
             v-if="file.type.startsWith('image')"/>
        <span v-else>{{file.type}}</span>
    </figure>
</template>

<script>
  export default {
    name: 'JinyaPageEditorPreviewElementFile',
    props: {
      file: {
        type: Object,
        required: true,
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page-editor__preview-figure {
        width: 100%;
        margin: 0;
    }

    .jinya-page-editor__preview-caption {
        width: 100%;
        display: block;
        font-size: 2rem;
        font-weight: lighter;
        margin-bottom: 1rem;
    }

    .jinya-page-editor__preview-image {
        width: 100%;
    }
</style>
