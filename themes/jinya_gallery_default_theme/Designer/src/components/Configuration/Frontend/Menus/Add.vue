<template>
  <jinya-menu-form :message="message" :state="state" :enable="enable" @save="save"/>
</template>

<script>
  import JinyaMenuForm from '@/components/Configuration/Frontend/Menus/MenuForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Timing from '@/framework/Utils/Timing';
  import Routes from '@/router/Routes';

  export default {
    name: 'Add',
    components: {
      JinyaMenuForm,
    },
    data() {
      return {
        state: '',
        message: '',
        enable: true,
      };
    },
    methods: {
      back() {
        this.$router.push(Routes.Configuration.Frontend.Menu.Overview);
      },
      async save(menu) {
        this.loading = true;
        this.enable = false;
        this.state = 'loading';

        try {
          this.message = Translator.message('configuration.frontend.menus.add.saving', menu);
          const savedMenu = await JinyaRequest.post('/api/menu', { name: menu.name });

          if (menu.logo) {
            this.message = Translator.message('configuration.frontend.menus.add.uploading', menu);
            await JinyaRequest.upload(`/api/menu/${savedMenu.id}/logo`, menu.logo);
          }

          this.message = Translator.message('configuration.frontend.menus.add.saving', menu);
          this.state = 'success';
          this.loading = false;

          await Timing.wait();
          this.$router.push({
            name: Routes.Configuration.Frontend.Menu.Editor.name,
            params: {
              id: savedMenu.id,
            },
          });
        } catch (e) {
          this.message = Translator.validator(e.message);
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>

<style scoped>

</style>
