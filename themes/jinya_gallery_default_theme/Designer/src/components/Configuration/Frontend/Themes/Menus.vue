<template>
    <jinya-loader v-if="loading" :loading="loading"/>
    <jinya-editor v-else>
        <jinya-message :message="message" :state="state"/>
        <jinya-form save-label="configuration.frontend.themes.menus.save" @submit="change" :enable="enable"
                    cancel-label="configuration.frontend.themes.menus.cancel" @back="cancel">
            <jinya-choice :choices="menus" @selected="primaryMenu = $event" :enable="enable"
                          :selected="primaryMenu"
                          label="configuration.frontend.themes.menus.primary"/>
            <jinya-choice :choices="menus" @selected="secondaryMenu = $event" :enable="enable"
                          :selected="secondaryMenu"
                          label="configuration.frontend.themes.menus.secondary"/>
            <jinya-choice :choices="menus" @selected="footerMenu = $event" :enable="enable"
                          :selected="footerMenu"
                          label="configuration.frontend.themes.menus.footer"/>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import Routes from '@/router/Routes';
  import Translator from '@/framework/i18n/Translator';
  import Timing from '@/framework/Utils/Timing';

  export default {
    name: 'Menus',
    components: {
      JinyaLoader,
      JinyaChoice,
      JinyaMessage,
      JinyaForm,
      JinyaEditor,
    },
    data() {
      return {
        state: '',
        message: '',
        primaryMenu: { text: '', value: '' },
        secondaryMenu: { text: '', value: '' },
        footerMenu: { text: '', value: '' },
        enable: true,
        loading: false,
        config: null,
        menus: [],
      };
    },
    async mounted() {
      this.loading = true;
      const configPromise = JinyaRequest.get(`/api/theme/${this.$route.params.name}`).then((config) => {
        this.config = config;
        if (!Array.isArray(config.menu.primary) || !config.menu.primary) {
          this.primaryMenu = {
            text: config.menu.primary.name,
            value: config.menu.primary.id,
          };
        }
        if (!Array.isArray(config.menu.secondary) || !config.menu.secondary) {
          this.secondaryMenu = {
            text: config.menu.secondary.name,
            value: config.menu.secondary.id,
          };
        }
        if (!Array.isArray(config.menu.footer) || !config.menu.footer) {
          this.footerMenu = {
            text: config.menu.footer.name,
            value: config.menu.footer.id,
          };
        }
      });
      const menuPromise = JinyaRequest.get('/api/menu?count=20000').then((items) => {
        const menus = items.items;
        this.menus = [
          {
            text: Translator.message('configuration.frontend.themes.menus.please_choose'),
            value: '',
          },
          ...menus.map(menu => ({
            text: menu.name,
            value: menu.id,
          })),
        ];
      });

      await Promise.all([menuPromise, configPromise]);
      this.loading = false;
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
              primary: { id: this.primaryMenu.value },
              secondary: { id: this.secondaryMenu.value },
              footer: { id: this.footerMenu.value },
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
