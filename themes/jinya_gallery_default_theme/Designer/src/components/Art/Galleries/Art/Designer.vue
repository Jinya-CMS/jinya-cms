<template>
  <div ref="designer" class="jinya-gallery-designer" :class="`is--${gallery.orientation}`" @wheel="scroll">
    <jinya-message :message="message" :state="state" v-if="state"/>
    <jinya-loader class="jinya-loader--designer" :loading="loading"/>
    <jinya-gallery-designer-button type="add" v-if="!loading" @click="add(-1)"/>
    <template v-if="!loading" v-for="(position, index) in artworks">
      <jinya-gallery-designer-item @wheel.native="scroll" :key="`${index}-${position.artwork.slug}`">
        <template>
          <jinya-gallery-designer-image @wheel.native="scroll" :src="position.artwork.picture"/>
          <jinya-gallery-designer-button @wheel.native="scroll" type="edit" @click="edit(position, index)"/>
          <jinya-gallery-designer-position-button v-if="index > 0" :decrease="true" @wheel.native="scroll"
                                                  @click="move(position, index, index - 1)"/>
          <jinya-gallery-designer-position-button v-if="index + 1 < artworks.length" @wheel.native="scroll"
                                                  :increase="true" @click="move(position, index, index + 1)"/>
        </template>
      </jinya-gallery-designer-item>
      <jinya-gallery-designer-button :key="`button-${index}-${position.artwork.slug}`" type="add"
                                     @wheel.native="scroll" @click="add(index)"/>
    </template>
    <jinya-gallery-designer-add-view @close="addModal.show = false" v-if="addModal.show" @picked="saveAdd"
                                     gallery-type="art"/>
    <jinya-gallery-designer-edit-view @close="editModal.show = false" v-if="editModal.show" @picked="saveEdit"
                                      @delete="deleteArtwork" gallery-type="art"/>
  </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import Translator from '@/framework/i18n/Translator';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaGalleryDesignerPositionButton from '@/components/Art/Galleries/Designer/PositionButton';
  import JinyaGalleryDesignerButton from '@/components/Art/Galleries/Designer/Button';
  import JinyaGalleryDesignerItem from '@/components/Art/Galleries/Designer/Item';
  import JinyaGalleryDesignerImage from '@/components/Art/Galleries/Designer/Image';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaGalleryDesignerAddView from '@/components/Art/Galleries/Designer/Add';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaGalleryDesignerEditView from '@/components/Art/Galleries/Designer/Edit';

  export default {
    components: {
      JinyaGalleryDesignerEditView,
      JinyaMessage,
      JinyaGalleryDesignerAddView,
      JinyaModal,
      JinyaGalleryDesignerImage,
      JinyaGalleryDesignerItem,
      JinyaGalleryDesignerButton,
      JinyaGalleryDesignerPositionButton,
      JinyaButton,
      JinyaLoader,
    },
    name: 'designer',
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
    methods: {
      scroll($event) {
        if (!$event.deltaX && !this.addModal.show && !this.editModal.show) {
          this.$refs.designer.scrollBy({
            behavior: 'auto',
            left: $event.deltaY > 0 ? 100 : -100,
          });
        }
      },
      async move(artworkPosition, oldPosition, newPosition) {
        if (oldPosition < newPosition) {
          this.artworks.splice(newPosition + 1, 0, artworkPosition);
          this.artworks.splice(oldPosition, 1);
        } else {
          this.artworks.splice(newPosition, 0, artworkPosition);
          this.artworks.splice(oldPosition + 1, 1);
        }
        await JinyaRequest.put(`/api/gallery/art/${this.gallery.slug}/artwork/${artworkPosition.id}/${oldPosition}`, {
          position: newPosition,
        });
      },
      async saveAdd(artwork) {
        const id = await JinyaRequest.post(`/api/gallery/art/${this.gallery.slug}/artwork`, {
          position: this.currentPosition,
          artwork: artwork.slug,
        });

        this.artworks.splice(this.currentPosition + 1, 0, {
          id,
          artwork,
        });
        this.addModal.show = false;
      },
      async saveEdit(artwork) {
        // eslint-disable-next-line max-len
        await JinyaRequest.put(`/api/gallery/art/${this.gallery.slug}/artwork/${this.artworkPosition.id}/${this.currentPosition}`, {
          artwork: artwork.slug,
        });

        this.artworks.splice(this.currentPosition, 1, {
          artwork,
          id: this.artworkPosition.id,
        });
        this.addModal.show = false;
      },
      async deleteArtwork() {
        await JinyaRequest.delete(`/api/gallery/art/${this.gallery.slug}/artwork/${this.artworkPosition.id}`);

        this.artworks.splice(this.currentPosition, 1);

        this.editModal.show = false;
      },
      add(position) {
        this.addModal.show = true;
        this.addModal.loading = true;
        this.currentPosition = position;
      },
      edit(artworkPosition, position) {
        this.editModal.show = true;
        this.editModal.loading = true;
        this.currentPosition = position;
        this.artworkPosition = artworkPosition;
      },
    },
    data() {
      return {
        gallery: {
          name: '',
          orientation: '',
          background: '',
          slug: '',
        },
        state: '',
        message: '',
        artworks: [],
        loading: false,
        addModal: {
          show: false,
          loading: false,
        },
        editModal: {
          show: false,
          loading: false,
        },
      };
    },
  };
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
