<template>
    <jinya-artwork-form :static="true" :artwork="artwork" @save="edit" :message="message" :state="state"
                        :hide-on-error="true" save-label="art.artworks.details.edit"/>
</template>

<script>
  import JinyaArtworkForm from "./ArtworkForm";
  import JinyaRequest from "../../../Framework/Ajax/JinyaRequest";
  import Routes from "../../../../router/Routes";
  import Translator from "../../../Framework/i18n/Translator";
  import DOMUtils from "@/components/Framework/Utils/DOMUtils";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaArtworkForm
    },
    name: "art-details",
    data() {
      return {
        message: '',
        state: '',
        artwork: {
          background: '',
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
      try {
        const artwork = await JinyaRequest.get(`/api/artwork/${this.$route.params.slug}`);
        this.artwork = artwork.item;
        this.state = '';
        DOMUtils.changeTitle(this.artwork.name);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.artworks.${error.message}`);
      }
    },
    methods: {
      edit() {
        this.$router.push({
          name: Routes.Art.Artworks.SavedInJinya.Edit.name,
          params: {
            slug: this.artwork.slug
          }
        });
      }
    }
  }
</script>
