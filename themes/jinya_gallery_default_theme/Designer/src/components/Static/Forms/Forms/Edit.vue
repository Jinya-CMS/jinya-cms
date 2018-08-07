<template>
  <jinya-form-form @save="save" :enable="enable" :message="message" :state="state" :form="form"/>
</template>

<script>
  import JinyaFormForm from '@/components/Static/Forms/Forms/FormForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Timing from '@/framework/Utils/Timing';
  import Routes from '@/router/Routes';
  import DOMUtils from '@/framework/Utils/DOMUtils';

  export default {
    name: 'Edit',
    components: {
      JinyaFormForm,
    },
    data() {
      return {
        message: '',
        state: '',
        enable: true,
        form: {
          title: '',
          description: '',
          toAddress: '',
          slug: '',
        },
      };
    },
    async mounted() {
      this.state = 'loading';
      this.enable = false;
      this.message = Translator.message('static.forms.forms.edit.loading');
      try {
        this.form = await JinyaRequest.get(`/api/form/${this.$route.params.slug}`);
        this.state = '';
        this.enable = true;
        DOMUtils.changeTitle(Translator.message('static.forms.forms.edit.title', this.form));
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(error.message);
      }
    },
    methods: {
      async save(form) {
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('static.forms.forms.add.saving', form);

          await JinyaRequest.put(`/api/form/${this.form.slug}`, form);

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
