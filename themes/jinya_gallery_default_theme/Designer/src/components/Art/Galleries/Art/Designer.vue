<template>
    <div class="jinya-gallery-designer" :class="`is--${gallery.orientation}`">
        <jinya-loader class="jinya-loader--designer" :loading="loading"/>
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
        <jinya-gallery-designer-add-view v-if="addModal.show" @picked="saveAdd"/>
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
  import JinyaModal from "@/components/Framework/Markup/Modal/Modal";
  import JinyaGalleryDesignerAddView from "@/components/Art/Galleries/Designer/Add";
  import JinyaMessage from "@/components/Framework/Markup/Validation/Message";

  export default {
    components: {
      JinyaMessage,
      JinyaGalleryDesignerAddView,
      JinyaModal,
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
        this.state = 'loading';
        this.message = Translator.message('art.galleries.designer.moving', artworkPosition.artwork);
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
        this.state = '';
        this.message = '';
      },
      async saveAdd(artwork) {
        this.state = 'loading';
        this.message = Translator.message('art.galleries.designer.add.pending', artwork);
        const id = await JinyaRequest.post(`/api/gallery/${this.gallery.slug}/artwork`, {
          position: this.currentPosition,
          artwork: artwork.slug
        });

        this.artworks.splice(this.currentPosition, {
          id: id,
          artwork: artwork
        });

        this.state = '';
        this.message = '';
      },
      add(position) {
        this.addModal.show = true;
        this.addModal.loading = true;
        this.currentPosition = position;
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
        state: '',
        message: '',
        artworks: [],
        loading: false,
        addModal: {
          show: false,
          loading: false
        }
      };
    }
  }
</script>

<style scoped lang="scss">
    .jinya-message--designer {
        margin-right: -12.5%;
        margin-left: -12.5%;
        width: 125%;
        padding-top: 1em;
    }
</style>

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