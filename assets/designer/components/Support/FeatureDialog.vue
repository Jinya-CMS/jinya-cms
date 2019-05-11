<template>
  <jinya-form @submit="submit">
    <jinya-modal :is-fullscreen="true" @close="$emit('close')" title="support.feature_dialog.title" v-if="show">
      <jinya-message :message="message" :state="state" class="is--feature-dialog" slot="message">
        <jinya-message-action-bar v-if="state === 'error'">
          <jinya-button href="mailto:developers@jinya.de" label="support.feature_dialog.send_mail"/>
        </jinya-message-action-bar>
      </jinya-message>
      <jinya-input :required="true" :validation-message="'support.feature_dialog.form.title.empty'|jvalidator"
                   label="support.feature_dialog.form.title"
                   v-model="title"/>
      <jinya-textarea :required="true" :validation-message="'support.feature_dialog.form.details.empty'|jvalidator"
                      label="support.feature_dialog.form.details"
                      v-model="details"/>
      <jinya-modal-button :closes-modal="true" :is-secondary="true" label="support.feature_dialog.form.cancel"
                          slot="buttons-left"/>
      <jinya-modal-button :is-success="true" label="support.feature_dialog.form.submit" slot="buttons-right"
                          type="submit"/>
    </jinya-modal>
  </jinya-form>
</template>

<script>
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaTextarea from '@/framework/Markup/Form/Textarea';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import Translator from '@/framework/i18n/Translator';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaForm from '@/framework/Markup/Form/Form';

  export default {
    name: 'jinya-feature-dialog',
    components: {
      JinyaForm,
      JinyaButton,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaInput,
      JinyaTextarea,
      JinyaModalButton,
      JinyaModal,
    },
    props: {
      show: {
        type: Boolean,
        default() {
          return false;
        },
      },
    },
    data() {
      return {
        title: '',
        details: '',
        message: Translator.message('support.feature_dialog.content'),
        state: 'primary',
      };
    },
    methods: {
      reset() {
        this.title = '';
        this.details = '';
        this.message = Translator.message('support.feature_dialog.content');
        this.state = 'primary';
      },
      async submit() {
        const data = {
          title: this.title,
          details: this.details,
        };

        try {
          this.message = Translator.message('support.feature_dialog.sending');
          this.state = 'loading';
          await JinyaRequest.post('/api/support/feature', data);
          this.$emit('close');
        } catch (e) {
          this.message = Translator.message('support.feature_dialog.error');
          this.state = 'error';
        }
      },
    },
  };
</script>

<style lang="scss" scoped>
  .is--feature-dialog {
    margin: 0;
    width: 100%;
  }
</style>
