<template>
  <jinya-loader :loading="loading" v-if="loading"/>
  <jinya-editor v-else>
    <jinya-message :message="message" :state="state"/>
    <jinya-form :enable="enable" @back="cancel" @submit="change"
                cancel-label="configuration.frontend.themes.links.cancel"
                save-label="configuration.frontend.themes.links.save"
                v-if="state !== 'info'">
    </jinya-form>
  </jinya-editor>
</template>

<script>
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import Routes from '@/router/Routes';
  import Translator from '@/framework/i18n/Translator';
  import Timing from '@/framework/Utils/Timing';

  export default {
    name: 'Links',
    components: {
      JinyaLoader,
      JinyaMessage,
      JinyaForm,
      JinyaEditor,
    },
    data() {
      return {
        state: '',
        message: '',
        enable: true,
        loading: false,
        structure: {},
        links: {},
        artworks: [],
        pages: [],
        videoGalleries: [],
        artGalleries: [],
        forms: [],
        menus: [],
      };
    },
    async mounted() {
      this.loading = true;
      this.structure = await JinyaRequest.get(`/api/theme/${this.$route.params.name}/links/structure`);

      if (this.structure.length === 0) {
        this.loading = false;
        this.state = 'info';
        this.message = 'configuration.frontend.themes.links.no_structure_found';
      } else {
        const linksPromise = JinyaRequest
          .get(`/api/theme/${this.$route.params.name}/links`)
          .then((links) => {
            this.links = links;
          });
        const artworksPromise = JinyaRequest
          .get('/api/artwork?count=20000000')
          .then((artworks) => {
            this.artworks = artworks.items;
          });
        const pagePromise = JinyaRequest
          .get('/api/page?count=20000000')
          .then((pages) => {
            this.pages = pages.items;
          });
        const formsPromise = JinyaRequest
          .get('/api/form?count=20000000')
          .then((forms) => {
            this.forms = forms.items;
          });
        const menusPromise = JinyaRequest
          .get('/api/menu?count=20000000')
          .then((menus) => {
            this.menus = menus.items;
          });
        const artGalleriesPromise = JinyaRequest
          .get('/api/gallery/art?count=20000000')
          .then((galleries) => {
            this.galleries = galleries.items;
          });
        const videoGalleriesPromise = JinyaRequest
          .get('/api/gallery/video?count=20000000')
          .then((galleries) => {
            this.galleries = galleries.items;
          });

        await Promise.all([
          linksPromise,
          pagePromise,
          formsPromise,
          artworksPromise,
          menusPromise,
          artGalleriesPromise,
          videoGalleriesPromise,
        ]);
        this.loading = false;
      }
    },
    methods: {
      cancel() {
        this.$router.push(Routes.Configuration.Frontend.Theme.Overview);
      },
      async change() {
        this.enable = false;
        try {
          this.state = 'loading';
          this.message = Translator.message('configuration.frontend.themes.menus.saving', this.config);

          await JinyaRequest.put(`/api/theme/${this.$route.params.name}`, {
            menus: {
              primary: {id: this.primaryMenu.value},
              secondary: {id: this.secondaryMenu.value},
              footer: {id: this.footerMenu.value},
            },
          });

          this.message = Translator.message('configuration.frontend.themes.menus.saved', this.config);
          this.state = 'success';

          await Timing.wait();
          this.$router.push(Routes.Configuration.Frontend.Theme.Overview);
        } catch (e) {
          this.enable = true;
          this.message = Translator.validator(`configuration.frontend.themes.menus.${e.message}`);
          this.state = 'error';
        }
      },
    },
  };
</script>
