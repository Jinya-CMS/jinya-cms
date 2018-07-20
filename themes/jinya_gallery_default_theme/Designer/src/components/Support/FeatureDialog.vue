<template>
    <jinya-modal @close="$emit('close')" title="support.feature_dialog.title" v-if="show" :is-fullscreen="true">
        <jinya-message slot="message" :message="message" :state="state">
            <jinya-message-action-bar v-if="state === 'error'">
                <jinya-button label="support.feature_dialog.send_mail" href="mailto:developers@jinya.de"/>
            </jinya-message-action-bar>
        </jinya-message>
        <jinya-input :required="true" label="support.feature_dialog.form.title" v-model="title"/>
        <jinya-textarea :required="true" label="support.feature_dialog.form.details" v-model="details"/>
        <jinya-modal-button slot="buttons-left" :closes-modal="true" label="support.feature_dialog.form.cancel"
                            :is-secondary="true"/>
        <jinya-modal-button slot="buttons-right" label="support.feature_dialog.form.submit" :is-success="true"
                            @click="submit"/>
    </jinya-modal>
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

  export default {
    name: 'jinya-feature-dialog',
    components: {
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
          const response = await JinyaRequest.post('/api/support/feature', data);
          window.open(response.followUpLink);
          this.$emit('close');
        } catch (e) {
          this.message = Translator.message('support.feature_dialog.error');
          this.state = 'error';
        }
      },
    },
  };
</script>
