<template>
    <div class="jinya-gallery-designer__masonry">
        <jinya-message :message="message" :state="state" v-if="state"/>
        <div class="jinya-gallery-designer-masonry__container is--masonry">
            <jinya-gallery-designer-button @click="add(index)" type="add"/>
            <template v-for="(position, index) in artworks" v-if="!loading">
                <jinya-gallery-designer-item :key="`${index}-${position.artwork.slug}`">
                    <div class="jinya-gallery-designer-masonry__element">
                        <jinya-gallery-designer-image :src="position.artwork.picture"/>
                        <jinya-gallery-designer-button @click="edit(position, index)" type="edit"/>
                        <jinya-gallery-designer-position-button :decrease="true"
                                                                @click="move(position, index, index - 1)"
                                                                v-if="index > 0"/>
                        <jinya-gallery-designer-position-button :increase="true"
                                                                @click="move(position, index, index + 1)"
                                                                v-if="index + 1 < artworks.length"/>
                    </div>
                </jinya-gallery-designer-item>
                <jinya-gallery-designer-button :key="`button-${index}-${position.artwork.slug}`" @click="add(index)"
                                               type="add"/>
            </template>
        </div>
        <jinya-gallery-designer-add-view @close="addModal.show = false" @picked="saveAdd" gallery-type="art"
                                         v-if="addModal.show"/>
        <jinya-gallery-designer-edit-view @close="editModal.show = false" @delete="deleteArtwork" @picked="saveEdit"
                                          gallery-type="art" v-if="editModal.show"/>
    </div>
</template>

<script>
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaGalleryDesignerItem from '../Designer/Item';
  import JinyaGalleryDesignerImage from '../Designer/Image';
  import JinyaGalleryDesignerButton from '../Designer/Button';
  import JinyaGalleryDesignerPositionButton from '../Designer/PositionButton';
  import JinyaGalleryDesignerAddView from '../Designer/Add';
  import JinyaGalleryDesignerEditView from '../Designer/Edit';

  export default {
    name: 'JinyaArtGalleryMasonryStyleDesigner',
    components: {
      JinyaGalleryDesignerEditView,
      JinyaGalleryDesignerAddView,
      JinyaGalleryDesignerPositionButton,
      JinyaGalleryDesignerButton,
      JinyaGalleryDesignerImage,
      JinyaGalleryDesignerItem,
      JinyaMessage,
      JinyaLoader,
    },
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

<style lang="scss" scoped>
    .jinya-gallery-designer-masonry__container {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        margin-top: 0.5rem;
    }

    .jinya-gallery-designer-masonry__element {
        margin: 1rem;
        display: grid;
        grid-template-columns: auto auto auto;

        &:first-child {
            margin-left: 0;
        }

        &:last-child {
            margin-left: 0;
        }
    }
</style>
