<template>
    <div class="jinya-artwork-edit">
        <jinya-message :message="message" :state="state" v-if="state"/>
        <jinya-artwork-form :artwork="artwork" @save="save" :enable="enable"/>
    </div>
</template>

<script>
  import JinyaFileInput from "../../../Framework/Markup/Form/FileInput";
  import JinyaForm from "../../../Framework/Markup/Form/Form";
  import JinyaArtworkForm from "./ArtworkForm";
  import JinyaRequest from "../../../Framework/Ajax/JinyaRequest";
  import JinyaMessage from "../../../Framework/Markup/Validation/Message";
  import Translator from "../../../Framework/i18n/Translator";
  import Routes from "../../../../router/Routes";
  import JinyaLoader from "../../../Framework/Markup/Loader";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaLoader,
      JinyaMessage,
      JinyaArtworkForm,
      JinyaForm,
      JinyaFileInput
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        artwork: {
          picture: '',
          name: '',
          slug: '',
          description: ''
        }
      };
    },
    name: "edit",
    async beforeCreate() {
      const artwork = await JinyaRequest.get(`/api/artwork/${this.$route.params.slug}`);
      this.artwork = artwork.item;
    },
    methods: {
      async save(artwork) {
        const picture = artwork.picture;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.artworks.edit.saving', {name: artwork.name});

          await JinyaRequest.put(`/api/artwork/${this.$route.params.slug}`, {
            name: artwork.name,
            slug: artwork.slug,
            description: artwork.description
          });

          if (picture) {
            this.message = Translator.message('art.artworks.edit.uploading', {name: artwork.name});
            await JinyaRequest.upload(`/api/artwork/${artwork.slug}/picture`, picture);
          }

          this.state = 'success';
          this.message = Translator.message('art.artworks.edit.success', {name: artwork.name});

          setTimeout(() => {
            this.$router.push({
              name: Routes.Art.Artworks.SavedInJinya.Details.name,
              query: {
                slug: this.$route.params.slug
              }
            });
          }, 0.5 * 60 * 1000);
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-artwork-edit {
        padding-top: 1em;
    }
</style>