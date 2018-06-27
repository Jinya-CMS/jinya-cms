<template>
    <jinya-modal modal-modifiers="jinya-modal--add-artwork" title="art.galleries.designer.add.title"
                 @close="$emit('close')">
        <jinya-message :message="'art.galleries.designer.artwork_view.loading'|jmessage" state="loading" v-if="loading"
                       slot="message"/>
        <jinya-gallery-designer-artwork-view v-if="galleryType === 'art'" @picked="pick" @load-start="loading = true"
                                             @load-end="loading = false"/>
        <jinya-modal-button slot="buttons-right" :closes-modal="true" label="art.galleries.designer.add_view.cancel"
                            :is-secondary="true" :is-disabled="picked"/>
    </jinya-modal>
</template>

<script>
  import JinyaModal from "@/framework/Markup/Modal/Modal";
  import JinyaModalButton from "@/framework/Markup/Modal/ModalButton";
  import JinyaGalleryDesignerArtworkView from "@/components/Art/Galleries/Designer/ArtworkView";
  import JinyaMessage from "@/framework/Markup/Validation/Message";

  export default {
    components: {
      JinyaMessage,
      JinyaGalleryDesignerArtworkView,
      JinyaModalButton,
      JinyaModal
    },
    name: "jinya-gallery-designer-add-view",
    props: {
      galleryType: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        loading: false,
        picked: false
      };
    },
    methods: {
      pick(item) {
        this.$emit('picked', item);
        this.picked = true;
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-modal--add-artwork {
        .jinya-modal__content {
            padding: 0;

        }
    }
</style>