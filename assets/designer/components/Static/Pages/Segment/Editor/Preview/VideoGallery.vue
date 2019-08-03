<template>
    <div>
        <span class="jinya-page-editor__preview-header">{{videoGallery.name}}</span>
        <jinya-loader loading="true" v-if="loading"/>
        <div class="jinya-page-editor__preview-gallery" v-else>
            <template v-for="position in videos">
                <video :key="position.video.slug" :poster="`/api/video/jinya/${position.video.slug}/poster`"
                       :src="`/api/video/jinya/${position.video.slug}/video`" class="jinya-page-editor__preview-video"
                       v-if="position.video"></video>
                <iframe :key="position.youtubeVideo.slug"
                        :src="`https://www.youtube-nocookie.com/embed/${position.youtubeVideo.videoKey}`"
                        class="jinya-page-editor__preview-video" v-if="position.youtubeVideo"></iframe>
            </template>
        </div>
    </div>
</template>

<script>
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';

  export default {
    name: 'JinyaPageEditorPreviewElementVideoGallery',
    components: { JinyaLoader },
    props: {
      videoGallery: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        videos: [],
        loading: false,
      };
    },
    async mounted() {
      this.loading = true;

      this.videos = await JinyaRequest.get(`/api/gallery/video/${this.videoGallery.slug}/video`);

      this.loading = false;
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page-editor__preview-header {
        font-size: 2rem;
        font-weight: lighter;
        margin-bottom: 1rem;
    }

    .jinya-page-editor__preview-gallery {
        display: flex;
        flex-flow: row wrap;
        margin: -0.5rem;
    }

    .jinya-page-editor__preview-image {
        object-fit: cover;
        flex: 1 1 7.5rem;
        padding: 0.5rem;
    }
</style>
