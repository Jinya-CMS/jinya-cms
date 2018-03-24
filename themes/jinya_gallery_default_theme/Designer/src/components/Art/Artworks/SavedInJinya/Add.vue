<template>
    <jinya-artwork-form @save="save"/>
</template>

<script>
  import JinyaFileInput from "../../../Framework/Markup/Form/FileInput";
  import JinyaForm from "../../../Framework/Markup/Form/Form";
  import JinyaArtworkForm from "./ArtworkForm";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaArtworkForm,
      JinyaForm,
      JinyaFileInput
    },
    name: "add",
    methods: {
      save() {

      }
    }
  }
</script>

<style scoped>

</style>