<template>
  <jinya-form-form :enable="enable" :message="message" :state="state" @save="save"/>
</template>

<script>
  import JinyaFormForm from '@/components/Static/Forms/Forms/FormForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Timing from '@/framework/Utils/Timing';
  import Routes from '@/router/Routes';

  export default {
    name: 'Add',
    components: {
      JinyaFormForm,
    },
    data() {
      return {
        message: '',
        state: '',
        enable: true,
      };
    },
    methods: {
      async save(form) {
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('static.forms.forms.add.saving', form);

          await JinyaRequest.post('/api/form', form);

          this.state = 'success';
          this.message = Translator.message('static.forms.forms.add.success', form);

          await Timing.wait();
          this.$router.push(Routes.Static.Forms.Forms.Overview);
        } catch (error) {
          this.message = Translator.validator(`static.forms.forms.${error.message}`);
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>

<style scoped>

</style>
