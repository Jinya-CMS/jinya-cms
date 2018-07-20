<template>
    <jinya-page-form @save="save" :enable="enable" :message="message" :state="state"/>
</template>

<script>
  import JinyaPageForm from '@/components/Static/Pages/SavedInJinya/PageForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Routes from '@/router/Routes';
  import Timing from '@/framework/Utils/Timing';

  export default {
    name: 'add',
    components: {
      JinyaPageForm,
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: true,
      };
    },
    methods: {
      async save(page) {
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('static.pages.add.saving', page);

          await JinyaRequest.post('/api/page', page);

          this.state = 'success';
          this.message = Translator.message('static.pages.add.success', page);

          await Timing.wait();
          this.$router.push(Routes.Static.Pages.SavedInJinya.Overview);
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>
