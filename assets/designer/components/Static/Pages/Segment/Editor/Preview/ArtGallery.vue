<template>
  <div>
    <span class="jinya-page-editor__preview-header">{{artGallery.name}}</span>
    <jinya-loader loading="true" v-if="loading"/>
    <div class="jinya-page-editor__preview-gallery" v-else>
      <img :alt="position.artwork.name" :key="position.artwork.slug"
           :src="`/api/artwork/${position.artwork.slug}/picture`" class="jinya-page-editor__preview-image"
           v-for="position in artworks"/>
    </div>
  </div>
</template>

<script>
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';

  export default {
    name: 'JinyaPageEditorPreviewElementArtGallery',
    components: { JinyaLoader },
    props: {
      artGallery: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        artworks: [],
        loading: false,
      };
    },
    async mounted() {
      this.loading = true;

      this.artworks = await JinyaRequest.get(`/api/gallery/art/${this.artGallery.slug}/artwork`);

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
