<template>
    <figure class="jinya-page-editor__preview-figure">
        <figcaption class="jinya-page-editor__preview-caption">{{artwork.name}}</figcaption>
        <img :alt="artwork.name" :src="`/api/artwork/${artwork.slug}/picture`"
             class="jinya-page-editor__preview-image"/>
    </figure>
</template>

<script>
  export default {
    name: 'JinyaPageEditorPreviewElementArtwork',
    props: {
      artwork: {
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
