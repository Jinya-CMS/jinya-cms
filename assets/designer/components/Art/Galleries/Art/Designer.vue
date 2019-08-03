<template>
    <jinya-loader :loading="loading" class="jinya-loader--designer" v-if="loading"/>
    <jinya-message :message="message" :state="state" v-else-if="state"/>
    <jinya-art-gallery-masonry-style-designer :artworks="artworks" :gallery="gallery" v-else-if="gallery.masonry"/>
    <jinya-art-gallery-list-style-designer :artworks="artworks" :gallery="gallery" v-else/>
</template>

<script>
  import JinyaArtGalleryListStyleDesigner from './ListStyleDesigner';
  import JinyaArtGalleryMasonryStyleDesigner from './MasonryStyleDesigner';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import Translator from '@/framework/i18n/Translator';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaMessage from '@/framework/Markup/Validation/Message';

  export default {
    components: {
      JinyaMessage,
      JinyaLoader,
      JinyaArtGalleryMasonryStyleDesigner,
      JinyaArtGalleryListStyleDesigner,
    },
    data() {
      return {
        loading: false,
        gallery: {},
        artworks: {},
        message: '',
        state: '',
      };
    },
    async mounted() {
      this.loading = true;
      try {
        const gallery = await JinyaRequest.get(`/api/gallery/art/${this.$route.params.slug}`);
        this.gallery = gallery.item;
        this.artworks = await JinyaRequest.get(`/api/gallery/art/${this.$route.params.slug}/artwork`);
        DOMUtils.changeTitle(Translator.message('art.galleries.designer.title', this.gallery));
      } catch (error) {
        this.message = Translator.message('art.galleries.designer.loading_failed');
        this.state = 'error';
      }
      this.loading = false;
    },
  };
</script>

<style lang="scss">
    .jinya-gallery-designer {
        height: 100%;
        width: 100%;
        display: grid;
        grid-gap: 1em;

        &.is--horizontal {
            padding-bottom: 10em;
            grid-template-columns: repeat(auto-fill, minmax(10em, 100%));
            grid-auto-flow: column;
            padding-top: 1em;
            overflow-x: auto;
            margin-right: -12.5%;
            margin-left: -12.5%;
            width: 125%;
        }

        &.is--vertical {
            grid-template-rows: repeat(auto-fill, minmax(10em, 100%));
            padding-top: 1em;
        }
    }
</style>
