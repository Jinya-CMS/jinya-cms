<template>
    <jinya-gallery-form :back-target="backRoute" :enable="enable" :gallery="gallery" :message="message" :state="state"
                        @save="save"/>
</template>

<script>
  import JinyaGalleryForm from '@/components/Art/Galleries/GalleryForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Routes from '@/router/Routes';
  import Timing from '@/framework/Utils/Timing';
  import DOMUtils from '@/framework/Utils/DOMUtils';

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaGalleryForm,
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
          description: '',
        },
      };
    },
    computed: {
      backRoute() {
        return Routes.Art.Galleries.Art.Overview;
      },
    },
    name: 'edit',
    async mounted() {
      this.state = 'loading';
      this.enable = false;
      this.message = Translator.message('art.galleries.details.loading');
      try {
        const gallery = await JinyaRequest.get(`/api/gallery/art/${this.$route.params.slug}`);
        this.gallery = gallery.item;
        this.state = '';
        this.enable = true;
        DOMUtils.changeTitle(Translator.message('art.galleries.edit.title', this.gallery));
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.galleries.${error.message}`);
      }
    },
    methods: {
      async save(gallery) {
        const { background } = gallery;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.galleries.edit.saving', { name: gallery.name });

          const savedData = await JinyaRequest.put(`/api/gallery/art/${this.gallery.slug}`, {
            name: gallery.name,
            slug: gallery.slug,
            description: gallery.description,
            orientation: gallery.orientation,
            masonry: gallery.masonry,
          });

          if (background) {
            this.message = Translator.message('art.galleries.edit.uploading', { name: gallery.name });
            await JinyaRequest.upload(`/api/gallery/art/${savedData.slug}/background`, background);
          }

          this.state = 'success';
          this.message = Translator.message('art.galleries.edit.success', { name: gallery.name });

          await Timing.wait();
          this.$router.push({
            name: Routes.Art.Galleries.Art.Details.name,
            params: {
              slug: savedData.slug,
            },
          });
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>
