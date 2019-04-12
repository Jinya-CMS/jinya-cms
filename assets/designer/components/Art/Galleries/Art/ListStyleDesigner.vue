<template>
  <div :class="`is--${gallery.orientation}`" @wheel="scroll" class="jinya-gallery-designer" ref="designer">
    <jinya-message :message="message" :state="state" v-if="state"/>
    <jinya-gallery-designer-button @click="add(-1)" type="add" v-if="!loading"/>
    <template v-for="(position, index) in artworks" v-if="!loading">
      <jinya-gallery-designer-item :key="`${index}-${position.artwork.slug}`" @wheel.native="scroll">
        <template>
          <jinya-gallery-designer-image :src="position.artwork.picture" @wheel.native="scroll"/>
          <jinya-gallery-designer-button @click="edit(position, index)" @wheel.native="scroll" type="edit"/>
          <jinya-gallery-designer-position-button :decrease="true" @click="move(position, index, index - 1)"
                                                  @wheel.native="scroll" v-if="index > 0"/>
          <jinya-gallery-designer-position-button :increase="true" @click="move(position, index, index + 1)"
                                                  @wheel.native="scroll" v-if="index + 1 < artworks.length"/>
        </template>
      </jinya-gallery-designer-item>
      <jinya-gallery-designer-button :key="`button-${index}-${position.artwork.slug}`" @click="add(index)"
                                     @wheel.native="scroll" type="add"/>
    </template>
    <jinya-gallery-designer-add-view @close="addModal.show = false" @picked="saveAdd" gallery-type="art"
                                     v-if="addModal.show"/>
    <jinya-gallery-designer-edit-view @close="editModal.show = false" @delete="deleteArtwork" @picked="saveEdit"
                                      gallery-type="art" v-if="editModal.show"/>
  </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
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
    name: 'JinyaArtGalleryListStyleDesigner',
    props: {
      gallery: {
        type: Object,
        required: true,
      },
      artworks: {
        type: Array,
        required: true,
      },
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
        this.editModal.show = false;
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
        state: '',
        message: '',
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

<style lang="scss" scoped>
  .jinya-message--designer {
    margin-right: -12.5%;
    margin-left: -12.5%;
    width: 125%;
    padding-top: 1em;
  }
</style>
