<template>
  <jinya-modal modal-modifiers="jinya-modal--edit-artwork" :title="`art.galleries.designer.edit.${galleryType}.title`"
               @close="$emit('close')">
    <jinya-message :message="`art.galleries.designer.${galleryType}_view.loading`|jmessage" state="loading"
                   v-if="loading"
                   slot="message"/>
    <jinya-gallery-designer-artwork-view v-if="galleryType === 'art'" @picked="pick" @load-start="loading = true"
                                         @load-end="loading = false"/>
    <jinya-gallery-designer-video-view v-else-if="galleryType === 'video'" @picked="pick"
                                       @load-start="loading = true" @load-end="loading = false"/>
    <jinya-modal-button slot="buttons-left" :label="`art.galleries.designer.edit_view.${galleryType}.delete`"
                        :is-danger="true" :is-disabled="picked" @click="$emit('delete')"/>
    <jinya-modal-button slot="buttons-right" :closes-modal="true"
                        :label="`art.galleries.designer.edit_view.${galleryType}.cancel`"
                        :is-secondary="true" :is-disabled="picked"/>
  </jinya-modal>
</template>

<script>
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaGalleryDesignerArtworkView from '@/components/Art/Galleries/Designer/ArtworkView';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaGalleryDesignerVideoView from '@/components/Art/Galleries/Designer/VideoView';

  export default {
    components: {
      JinyaGalleryDesignerVideoView,
      JinyaMessage,
      JinyaGalleryDesignerArtworkView,
      JinyaModalButton,
      JinyaModal,
    },
    name: 'jinya-gallery-designer-edit-view',
    props: {
      galleryType: {
        type: String,
        required: true,
      },
    },
    data() {
      return {
        loading: false,
        picked: false,
      };
    },
    methods: {
      pick(item) {
        this.$emit('picked', item);
        this.picked = true;
      },
    },
  };
</script>

<style scoped lang="scss">
  .jinya-modal--edit-artwork {
    .jinya-modal__content {
      padding: 0;

    }
  }
</style>
