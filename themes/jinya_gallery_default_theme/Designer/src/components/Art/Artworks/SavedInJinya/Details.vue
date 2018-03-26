<template>
    <div class="jinya-artwork-details">
        <jinya-message :message="message" :state="state" v-if="state"/>
        <jinya-artwork-form :static="true" :artwork="artwork" save-label="art.artworks.details.edit" @save="edit"/>
    </div>
</template>

<script>
  import JinyaFileInput from "../../../Framework/Markup/Form/FileInput";
  import JinyaForm from "../../../Framework/Markup/Form/Form";
  import JinyaArtworkForm from "./ArtworkForm";
  import JinyaRequest from "../../../Framework/Ajax/JinyaRequest";
  import JinyaMessage from "../../../Framework/Markup/Validation/Message";
  import Routes from "../../../../router/Routes";
  import JinyaLoader from "../../../Framework/Markup/Loader";
  import JinyaButton from "../../../Framework/Markup/Button";
  import Translator from "../../../Framework/i18n/Translator";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaButton,
      JinyaLoader,
      JinyaMessage,
      JinyaArtworkForm,
      JinyaForm,
      JinyaFileInput
    },
    name: "art-details",
    data() {
      return {
        message: '',
        state: '',
        artwork: {
          picture: '',
          name: '',
          slug: '',
          description: ''
        },
        overviewRoute: Routes.Art.Artworks.SavedInJinya.Overview.name
      };
    },
    async mounted() {
      this.state = 'loading';
      this.message = Translator.message('art.artworks.details.loading');
      const artwork = await JinyaRequest.get(`/api/artwork/${this.$route.params.slug}`);
      this.artwork = artwork.item;
      this.state = '';
    },
    methods: {
      edit() {
        this.$router.push({
          name: Routes.Art.Artworks.SavedInJinya.Edit.name,
          params: {
            slug: this.slug
          }
        });
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-artwork-details {
        padding-top: 1em;
    }
</style>