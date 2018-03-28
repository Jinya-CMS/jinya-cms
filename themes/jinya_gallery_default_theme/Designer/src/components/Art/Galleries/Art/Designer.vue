<template>
    <div class="jinya-gallery-designer" :class="`is--${gallery.orientation}`">
        <jinya-loader :loading="loading"/>
        <div class="jinya-gallery-designer__button" v-if="!loading">
            <i class="mdi mdi-plus"></i>
        </div>
        <template v-if="!loading">
            <div class="jinya-gallery-designer__item">
                <figure class="jinya-gallery-designer__content" v-for="artwork in artworks">
                    <img :src="artwork.picture" class="jinya-gallery-designer__image">
                    <figcaption class="jinya-gallery-designer__image-name">{{gallery.name}}</figcaption>
                </figure>
                <div class="jinya-gallery-designer__button-move--left"></div>
                <div class="jinya-gallery-designer__button-move--right"></div>
            </div>
            <div class="jinya-gallery-designer__button">
                <i class="mdi mdi-plus"></i>
            </div>
        </template>
    </div>
</template>

<script>
  import JinyaRequest from "@/components/Framework/Ajax/JinyaRequest";
  import JinyaLoader from "@/components/Framework/Markup/Loader";

  export default {
    components: {JinyaLoader},
    name: "designer",
    props: {
      gallery: {
        type: Object,
        default() {
          return {
            orientation: 'horizontal',
            name: ''
          }
        }
      },
      artworks: {
        type: Array,
        default() {
          return [];
        }
      }
    },
    async mounted() {
      this.loading = true;
      try {
        this.artworks = await JinyaRequest.get(`/api/gallery/${this.$route.params.slug}/artwork`);
      } catch (error) {
      }
      this.loading = false;
    },
    data() {
      return {
        artworks: gallery.artworks,
        loading: false
      };
    }
  }
</script>

<style scoped lang="scss">

</style>