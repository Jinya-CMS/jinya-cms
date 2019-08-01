<template>
    <div class="jinya-page-editor__preview-pane">
        <section :key="segment.id" class="jinya-page-editor__preview-element" v-for="segment in segments">
            <jinya-page-editor-preview-element-artwork :artwork="segment.artwork"/>
            <jinya-page-editor-preview-element-html :html="segment.html"/>
            <jinya-page-editor-preview-element-video :video="segment.video"/>
            <jinya-page-editor-preview-element-youtube-video :youtube-video="segment.youtubeVideo"/>
            <jinya-page-editor-preview-element-art-gallery :art-gallery="segment.artGallery"/>
            <jinya-page-editor-preview-element-video-gallery :video-gallery="segment.videoGallery"/>
            <hr class="jinya-segment-page__divider"/>
        </section>
    </div>
</template>

<script>
  import JinyaPageEditorPreviewElementArtwork from '@/components/Static/Pages/Segment/Editor/Preview/Artwork';
  import JinyaPageEditorPreviewElementHtml from '@/components/Static/Pages/Segment/Editor/Preview/Html';
  import JinyaPageEditorPreviewElementVideo from '@/components/Static/Pages/Segment/Editor/Preview/Video';
  import JinyaPageEditorPreviewElementYoutubeVideo from '@/components/Static/Pages/Segment/Editor/Preview/YoutubeVideo';
  import JinyaPageEditorPreviewElementArtGallery from '@/components/Static/Pages/Segment/Editor/Preview/ArtGallery';
  import JinyaPageEditorPreviewElementVideoGallery from '@/components/Static/Pages/Segment/Editor/Preview/VideoGallery';

  export default {
    name: 'JinyaPageEditorPreviewPane',
    components: {
      JinyaPageEditorPreviewElementVideoGallery,
      JinyaPageEditorPreviewElementArtGallery,
      JinyaPageEditorPreviewElementYoutubeVideo,
      JinyaPageEditorPreviewElementVideo,
      JinyaPageEditorPreviewElementHtml,
      JinyaPageEditorPreviewElementArtwork,
    },
    props: {
      segments: {
        type: Array,
        required: true,
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page-editor__preview-pane {
        width: 100%;
        height: 100%;
        overflow-x: hidden;
    }

    .jinya-page-editor__preview-element {
        width: 100%;
    }

    .jinya-segment-page__divider {
        border: none;
        border-bottom: 1px solid $secondary;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
</style>
