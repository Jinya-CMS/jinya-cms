<template>
  <jinya-modal :title="`art.galleries.designer.add.${galleryType}.title`" @close="$emit('close')"
               modal-modifiers="jinya-modal--add-artwork">
    <jinya-message :message="`art.galleries.designer.${galleryType}_view.loading`|jmessage" slot="message"
                   state="loading"
                   v-if="loading"/>
    <jinya-gallery-designer-artwork-view @load-end="loading = false" @load-start="loading = true" @picked="pick"
                                         v-if="galleryType === 'art'"/>
    <jinya-gallery-designer-video-view @load-end="loading = false" @load-start="loading = true"
                                       @picked="pick" v-else-if="galleryType === 'video'"/>
    <jinya-modal-button :closes-modal="true" :is-disabled="picked"
                        :is-secondary="true"
                        :label="`art.galleries.designer.add_view.${galleryType}.cancel`" slot="buttons-right"/>
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
    name: 'jinya-gallery-designer-add-view',
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

<style lang="scss" scoped>
  .jinya-modal--add-artwork {
    .jinya-modal__content {
      padding: 0;

    }
  }
</style>
