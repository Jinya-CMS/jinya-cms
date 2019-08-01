<template>
    <figure class="jinya-page-editor__preview-figure">
        <figcaption class="jinya-page-editor__preview-caption">{{video.name}}</figcaption>
        <video :poster="`/api/video/jinya/${video.slug}/poster`" :src="`/api/video/jinya/${video.slug}/video`"
               class="jinya-page-editor__preview-video"></video>
    </figure>
</template>

<script>
  export default {
    name: 'JinyaPageEditorPreviewElementVideo',
    props: {
      video: {
        type: Object,
        required: true,
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page-editor__preview-figure {
        width: 100%;
    }

    .jinya-page-editor__preview-caption {
        width: 100%;
        display: block;
        font-size: 2rem;
        font-weight: lighter;
        margin-bottom: 1rem;
    }

    .jinya-page-editor__preview-video {
        width: 100%;
    }
</style>
