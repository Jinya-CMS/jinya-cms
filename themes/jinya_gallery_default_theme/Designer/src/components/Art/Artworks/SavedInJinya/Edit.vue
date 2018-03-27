<template>
    <jinya-artwork-form :artwork="artwork" @save="save" :enable="enable" :message="message" :state="state"/>
</template>

<script>
  import JinyaArtworkForm from "./ArtworkForm";
  import JinyaRequest from "../../../Framework/Ajax/JinyaRequest";
  import Translator from "../../../Framework/i18n/Translator";
  import Routes from "../../../../router/Routes";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaArtworkForm
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: false,
        artwork: {
          picture: '',
          name: '',
          slug: '',
          description: ''
        }
      };
    },
    name: "edit",
    async mounted() {
      this.state = 'loading';
      this.enable = false;
      this.message = Translator.message('art.artworks.details.loading');
      try {
        const artwork = await JinyaRequest.get(`/api/artwork/${this.$route.params.slug}`);
        this.artwork = artwork.item;
        this.state = '';
        this.enable = true;
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.artworks.${error.message}`);
      }
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
              params: {
                slug: this.artwork.slug
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

    .jinya-message__action-bar {
        display: block;
        padding-top: 1em;
    }
</style>