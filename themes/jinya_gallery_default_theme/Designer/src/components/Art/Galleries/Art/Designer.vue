<template>
    <div class="jinya-gallery-designer" :class="`is--${gallery.orientation}`">
        <jinya-loader :loading="loading"/>
        <jinya-gallery-designer-button type="add" v-if="!loading" @click="add(0)"/>
        <template v-if="!loading" v-for="(position, index) in artworks">
            <jinya-gallery-designer-item>
                <template>
                    <jinya-gallery-designer-image :src="position.artwork.picture"/>
                    <jinya-gallery-designer-button type="edit" @click="edit(position, index)"/>
                    <jinya-gallery-designer-position-button v-if="index > 0" :decrease="true"
                                                            @click="move(position, index, index - 1)"/>
                    <jinya-gallery-designer-position-button v-if="index + 1 < artworks.length" :increase="true"
                                                            @click="move(position, index, index + 1)"/>
                </template>
            </jinya-gallery-designer-item>
            <jinya-gallery-designer-button type="add" @click="add(index + 1)"/>
        </template>
    </div>
</template>

<script>
  import JinyaRequest from "@/components/Framework/Ajax/JinyaRequest";
  import JinyaLoader from "@/components/Framework/Markup/Loader";
  import DOMUtils from "@/components/Framework/Utils/DOMUtils";
  import Translator from "@/components/Framework/i18n/Translator";
  import JinyaButton from "@/components/Framework/Markup/Button";
  import JinyaGalleryDesignerPositionButton from "@/components/Art/Galleries/Designer/PositionButton";
  import JinyaGalleryDesignerButton from "@/components/Art/Galleries/Designer/Button";
  import JinyaGalleryDesignerItem from "@/components/Art/Galleries/Designer/Item";
  import JinyaGalleryDesignerImage from "@/components/Art/Galleries/Designer/Image";

  export default {
    components: {
      JinyaGalleryDesignerImage,
      JinyaGalleryDesignerItem,
      JinyaGalleryDesignerButton,
      JinyaGalleryDesignerPositionButton,
      JinyaButton,
      JinyaLoader
    },
    name: "designer",
    async mounted() {
      this.loading = true;
      try {
        const gallery = await JinyaRequest.get(`/api/gallery/${this.$route.params.slug}`);
        this.gallery = gallery.item;
        this.artworks = await JinyaRequest.get(`/api/gallery/${this.$route.params.slug}/artwork`);
        DOMUtils.changeTitle(Translator.message('art.galleries.designer.title', this.gallery));
      } catch (error) {
      }
      this.loading = false;
    },
    methods: {
      async move(artworkPosition, oldPosition, newPosition) {
        if (oldPosition < newPosition) {
          this.artworks.splice(newPosition + 1, 0, artworkPosition);
          this.artworks.splice(oldPosition, 1);
        } else {
          this.artworks.splice(newPosition, 0, artworkPosition);
          this.artworks.splice(oldPosition + 1, 1);
        }
        await JinyaRequest.put(`/api/gallery/${this.gallery.slug}/artwork/${artworkPosition.id}/${oldPosition}`, {
          position: newPosition
        });
      }
    },
    data() {
      return {
        gallery: {
          name: '',
          orientation: '',
          background: '',
          slug: ''
        },
        artworks: [],
        loading: false
      };
    }
  }
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
            margin-right: -10%;
            margin-left: -10%;
            width: 120%;
        }

        &.is--vertical {
            grid-template-rows: repeat(auto-fill, minmax(10em, 100%));
            padding-top: 10em;

            .jinya-gallery-designer__image {
                height: auto;
                width: 100%;
                grid-row: 2;
                grid-column: 1;
            }
        }
    }
</style>