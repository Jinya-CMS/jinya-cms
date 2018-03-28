<template>
    <jinya-gallery-form :static="true" :gallery="gallery" @save="edit" :message="message" :state="state"
                        :hide-on-error="true" save-label="art.galleries.details.edit"/>
</template>

<script>
  import JinyaGalleryForm from "../GalleryForm";
  import Routes from "../../../../router/Routes";
  import Translator from "../../../Framework/i18n/Translator";
  import JinyaRequest from "../../../Framework/Ajax/JinyaRequest";
  import DOMUtils from "@/components/Framework/Utils/DOMUtils";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaGalleryForm
    },
    name: "art-gallery-details",
    data() {
      return {
        message: '',
        state: '',
        gallery: {
          background: '',
          name: '',
          slug: '',
          description: ''
        },
        overviewRoute: Routes.Art.Galleries.Art.Overview.name
      };
    },
    async mounted() {
      this.state = 'loading';
      this.message = Translator.message('art.galleries.details.loading');
      try {
        const gallery = await JinyaRequest.get(`/api/gallery/${this.$route.params.slug}`);
        this.gallery = gallery.item;
        this.state = '';
        DOMUtils.changeTitle(this.gallery.name);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.galleries.${error.message}`);
      }
    },
    methods: {
      edit() {
        this.$router.push({
          name: Routes.Art.Galleries.Art.Edit.name,
          params: {
            slug: this.gallery.slug
          }
        });
      }
    }
  }
</script>