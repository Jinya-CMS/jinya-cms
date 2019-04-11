<template>
  <jinya-card :header="artwork.name" :key="artwork.slug" @mouseenter.native="isHovered = true"
              @mouseleave.native="isHovered=false">
    <img :alt="artwork.name" :src="artwork.picture" :style="dimensions" class="jinya-art-picture"/>
    <jinya-card-button :to="{name: detailsRoute, params: {slug: artwork.slug}}" icon="monitor" slot="footer"
                       type="details"/>
    <jinya-card-button :to="{name: editRoute, params: {slug: artwork.slug}}" icon="pencil" slot="footer"
                       type="edit"/>
    <jinya-card-button @click="showDeleteModal(artwork)" icon="delete" slot="footer" type="delete"/>
  </jinya-card>
</template>

<script>
  import JinyaCard from '@/framework/Markup/Listing/Card/Card';
  import JinyaCardButton from '@/framework/Markup/Listing/Card/CardButton';
  import Routes from '@/router/Routes';

  export default {
    name: 'JinyaArtworkCard',
    components: {
      JinyaCardButton,
      JinyaCard,
    },
    props: {
      showDeleteModal: {
        type: Function,
        required: true,
      },
      artwork: {
        type: Object,
        required: true,
      },
    },
    computed: {
      dimensions() {
        const aspectRatio = this.artwork.dimensions.width / this.artwork.dimensions.height;

        return {
          height: '15em',
          width: this.isHovered ? `${(15 * aspectRatio).toString(10)}em` : '100%',
        };
      },
      editRoute() {
        return Routes.Art.Artworks.SavedInJinya.Edit.name;
      },
      detailsRoute() {
        return Routes.Art.Artworks.SavedInJinya.Details.name;
      },
    },
    data() {
      return {
        isHovered: false,
      };
    },
  };
</script>

<style scoped lang="scss">
  .jinya-art-picture {
    width: 100%;
    height: 15em;
    object-fit: cover;
    transition: all 0.3s;
    justify-self: center;
    margin-right: auto;
    margin-left: auto;
  }
</style>
