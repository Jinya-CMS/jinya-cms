<template>
  <jinya-page-form :enable="enable" :message="message" :state="state" @save="save"/>
</template>

<script>
  import JinyaPageForm from '@/components/Static/Pages/Segment/PageForm';
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
          this.message = Translator.message('static.pages.segment.add.saving', page);

          await JinyaRequest.post('/api/segment_page', page);

          this.state = 'success';
          this.message = Translator.message('static.pages.segment.add.success', page);

          await Timing.wait();
          this.$router.push(Routes.Static.Pages.Segment.Overview);
        } catch (error) {
          this.message = Translator.validator(error.message);
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>
