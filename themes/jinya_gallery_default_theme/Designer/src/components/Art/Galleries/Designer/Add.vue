<template>
    <jinya-modal title="art.galleries.designer.add.title" :loading="loading">
        <jinya-modal-button slot="buttons-right" label="art.galleries.designer.add_view.cancel" :is-secondary="true"
                            :is-disabled="loading"/>
        <jinya-modal-button slot="buttons-right" label="art.galleries.designer.add_view.add" :is-primary="true"
                            :is-disabled="loading"/>
    </jinya-modal>
</template>

<script>
  import JinyaModal from "@/components/Framework/Markup/Modal/Modal";
  import JinyaModalButton from "@/components/Framework/Markup/Modal/ModalButton";

  export default {
    components: {
      JinyaModalButton,
      JinyaModal
    },
    name: "jinya-gallery-designer-add-view",
    data() {
      return {
        loading: true
      }
    }
  }
</script>

<style scoped>

</style>