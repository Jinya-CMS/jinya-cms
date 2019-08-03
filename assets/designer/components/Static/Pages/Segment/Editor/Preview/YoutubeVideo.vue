<template>
    <figure class="jinya-page-editor__preview-figure">
        <figcaption class="jinya-page-editor__preview-caption">{{youtubeVideo.name}}</figcaption>
        <iframe :src="`https://www.youtube-nocookie.com/embed/${youtubeVideo.videoKey}`"
                class="jinya-page-editor__preview-video"></iframe>
    </figure>
</template>

<script>
  export default {
    name: 'JinyaPageEditorPreviewElementYoutubeVideo',
    props: {
      youtubeVideo: {
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

    .jinya-page-editor__preview-image {
        width: 100%;
    }

    .jinya-page-editor__preview-video {
        width: 100%;
        height: 56.25%;
    }
</style>
