<template>
    <jinya-modal modal-modifiers="jinya-modal--edit-artwork" title="art.galleries.designer.edit.title"
                 @close="$emit('close')">
        <jinya-message message="art.galleries.designer.artwork_view.loading" state="loading" v-if="loading"
                       slot="message"/>
        <jinya-gallery-designer-artwork-view @picked="pick" @load-start="loading = true" @load-end="loading = false"/>
        <jinya-modal-button slot="buttons-left" label="art.galleries.designer.edit_view.delete"
                            :is-danger="true" :is-disabled="picked" @click="$emit('delete')"/>
        <jinya-modal-button slot="buttons-right" :closes-modal="true" label="art.galleries.designer.edit_view.cancel"
                            :is-secondary="true" :is-disabled="picked"/>
    </jinya-modal>
</template>

<script>
  import JinyaModal from "@/components/Framework/Markup/Modal/Modal";
  import JinyaModalButton from "@/components/Framework/Markup/Modal/ModalButton";
  import JinyaGalleryDesignerArtworkView from "@/components/Art/Galleries/Designer/ArtworkView";
  import JinyaMessage from "@/components/Framework/Markup/Validation/Message";

  export default {
    components: {
      JinyaMessage,
      JinyaGalleryDesignerArtworkView,
      JinyaModalButton,
      JinyaModal
    },
    name: "jinya-gallery-designer-edit-view",
    data() {
      return {
        loading: false,
        picked: false
      };
    },
    methods: {
      pick(artwork) {
        this.$emit('picked', artwork);
        this.picked = true;
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-modal--edit-artwork {
        .jinya-modal__content {
            padding: 0;

        }
    }
</style>