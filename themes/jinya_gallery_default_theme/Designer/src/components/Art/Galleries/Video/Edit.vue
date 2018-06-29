<template>
    <jinya-gallery-form :back-target="backRoute" :gallery="gallery" @save="save" :enable="enable" :message="message"
                        :state="state"/>
</template>

<script>
  import JinyaGalleryForm from "@/components/Art/Galleries/GalleryForm";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import Translator from "@/framework/i18n/Translator";
  import Routes from "@/router/Routes";
  import Timing from "@/framework/Utils/Timing";
  import DOMUtils from "@/framework/Utils/DOMUtils";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaGalleryForm
    },
    computed: {
      backRoute() {
        return Routes.Art.Galleries.Art.Overview;
      }
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: false,
        gallery: {
          background: '',
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
      this.message = Translator.message('art.galleries.details.loading');
      try {
        const gallery = await JinyaRequest.get(`/api/gallery/video/${this.$route.params.slug}`);
        this.gallery = gallery.item;
        this.state = '';
        this.enable = true;
        DOMUtils.changeTitle(Translator.message('art.galleries.art.edit.title', this.gallery));
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.galleries.${error.message}`);
      }
    },
    methods: {
      async save(gallery) {
        const background = gallery.background;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.galleries.edit.saving', {name: gallery.name});

          await JinyaRequest.put(`/api/gallery/video/${this.$route.params.slug}`, {
            name: gallery.name,
            slug: gallery.slug,
            description: gallery.description,
            orientation: gallery.orientation
          });

          if (background) {
            this.message = Translator.message('art.galleries.edit.uploading', {name: gallery.name});
            await JinyaRequest.upload(`/api/gallery/video/${gallery.slug}/background`, background);
          }

          this.state = 'success';
          this.message = Translator.message('art.galleries.edit.success', {name: gallery.name});

          await Timing.wait();
          this.$router.push({
            name: Routes.Art.Galleries.Video.Details.name,
            params: {
              slug: this.gallery.slug
            }
          });
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      }
    }
  }
</script>