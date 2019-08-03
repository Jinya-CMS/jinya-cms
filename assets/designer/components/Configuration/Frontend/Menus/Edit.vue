<template>
    <div>
        <jinya-loader :loading="loading" v-if="loading"/>
        <jinya-menu-form :enable="enable" :menu="menu" :message="message" :state="state" @back="back" @save="save"
                         v-else/>
    </div>
</template>

<script>
  import JinyaMenuForm from '@/components/Configuration/Frontend/Menus/MenuForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import Routes from '@/router/Routes';
  import Timing from '@/framework/Utils/Timing';

  export default {
    name: 'Edit',
    components: { JinyaLoader, JinyaMenuForm },
    data() {
      return {
        menu: {
          name: '',
          logo: '',
          id: -1,
        },
        loading: false,
        state: '',
        message: '',
        enable: true,
      };
    },
    async mounted() {
      this.loading = true;

      try {
        this.menu = await JinyaRequest.get(`/api/menu/${this.$route.params.id}`);
        EventBus.$emit(Events.header.change, Translator.message('configuration.frontend.menus.edit.title', this.menu));
        DOMUtils.changeTitle(Translator.message('configuration.frontend.menus.edit.title', this.menu));
      } catch (e) {
        this.state = 'error';
        this.message = Translator.validator(e.message);
      }

      this.loading = false;
    },
    methods: {
      back() {
        this.$router.push(Routes.Configuration.Frontend.Menu.Overview);
      },
      async save(menu) {
        this.enable = false;
        this.state = 'loading';

        try {
          this.message = Translator.message('configuration.frontend.menus.add.saving', menu);
          await JinyaRequest.put(`/api/menu/${this.$route.params.id}`, { name: menu.name });

          if (menu.logo) {
            this.message = Translator.message('configuration.frontend.menus.edit.uploading', menu);
            await JinyaRequest.upload(`/api/menu/${this.$route.params.id}/logo`, menu.logo);
          } else if (menu.logo === undefined) {
            this.message = Translator.message('configuration.frontend.menus.edit.deleting', menu);
            await JinyaRequest.delete(`/api/menu/${this.$route.params.id}/logo`);
          }

          this.message = Translator.message('configuration.frontend.menus.edit.success', menu);
          this.state = 'success';

          await Timing.wait();
          this.$router.push({
            name: Routes.Configuration.Frontend.Menu.Edit.name,
            params: {
              id: this.$route.params.id,
            },
          });
        } catch (e) {
          this.message = Translator.validator(`configuration.menus.${e.message}`);
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>

<style scoped>

</style>
