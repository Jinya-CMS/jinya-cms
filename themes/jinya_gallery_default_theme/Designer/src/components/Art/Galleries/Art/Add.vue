<template>
    <jinya-gallery-form @save="save" :enable="enable" :message="message" :state="state"/>
</template>

<script>
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import Translator from "@/framework/i18n/Translator";
  import Routes from "../../../../router/Routes";
  import JinyaGalleryForm from "../GalleryForm";
  import Timing from "@/framework/Utils/Timing";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {JinyaGalleryForm},
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: true
      }
    },
    name: "add",
    methods: {
      async save(gallery) {
        const background = gallery.background;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.galleries.add.saving', {name: gallery.name});

          await JinyaRequest.post('/api/gallery/art', {
            name: gallery.name,
            slug: gallery.slug,
            description: gallery.description,
            orientation: gallery.orientation
          });

          if (background) {
            this.message = Translator.message('art.galleries.add.uploading', {name: gallery.name});
            await JinyaRequest.upload(`/api/gallery/${gallery.slug}/background`, background);
          }

          this.state = 'success';
          this.message = Translator.message('art.galleries.add.success', {name: gallery.name});

          await Timing.wait();
          this.$router.push(Routes.Art.Galleries.Art.Overview);
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
    .jinya-gallery-add {
        padding-top: 1em;
    }
</style>