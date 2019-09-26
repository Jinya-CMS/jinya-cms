<template>
    <div class="jinya-page-editor__preview-pane">
        <section :key="segment.id" class="jinya-page-editor__preview-element" v-for="segment in segments">
            <jinya-page-editor-preview-element-artwork :artwork="segment.artwork" v-if="segment.artwork"/>
            <jinya-page-editor-preview-element-html :html="segment.html" v-if="segment.html"/>
            <jinya-page-editor-preview-element-video :video="segment.video" v-if="segment.video"/>
            <jinya-page-editor-preview-element-youtube-video :youtube-video="segment.youtubeVideo"
                                                             v-if="segment.youtubeVideo"/>
            <jinya-page-editor-preview-element-art-gallery :art-gallery="segment.artGallery" v-if="segment.artGallery"/>
            <jinya-page-editor-preview-element-video-gallery :video-gallery="segment.videoGallery"
                                                             v-if="segment.videoGallery"/>
            <jinya-page-editor-preview-element-gallery :gallery="segment.gallery" v-if="segment.gallery"/>
            <jinya-page-editor-preview-element-file :file="segment.file" v-if="segment.file"/>
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
  import JinyaPageEditorPreviewElementGallery from '@/components/Static/Pages/Segment/Editor/Preview/MediaGallery';
  import JinyaPageEditorPreviewElementFile from '@/components/Static/Pages/Segment/Editor/Preview/File';

  export default {
    name: 'JinyaPageEditorPreviewPane',
    components: {
      JinyaPageEditorPreviewElementFile,
      JinyaPageEditorPreviewElementGallery,
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
