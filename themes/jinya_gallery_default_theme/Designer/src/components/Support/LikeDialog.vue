<template>
    <jinya-modal @close="$emit('close')" title="support.like_dialog.title" v-if="show" :is-fullscreen="true">
        <jinya-message slot="message" :message="message" :state="state"/>
        <jinya-textarea :required="true" label="support.like_dialog.form.details" v-model="details"/>
        <jinya-modal-button slot="buttons-left" :closes-modal="true" label="support.like_dialog.form.cancel"
                            :is-secondary="true"/>
        <jinya-modal-button slot="buttons-right" label="support.like_dialog.form.submit" :is-success="true"
                            @click="submit"/>
    </jinya-modal>
</template>

<script>
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaTextarea from '@/framework/Markup/Form/Textarea';
  import Translator from '@/framework/i18n/Translator';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';

  export default {
    name: 'jinya-like-dialog',
    components: {
      JinyaMessage,
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
        details: '',
        message: Translator.message('support.like_dialog.content'),
        state: 'primary',
      };
    },
    methods: {
      reset() {
        this.details = '';
        this.message = Translator.message('support.like_dialog.content');
        this.state = 'primary';
      },
      async submit() {
        const data = {
          message: this.details,
        };

        JinyaRequest.post('/api/support/like', data).then(() => {
        });
        this.$emit('close');
      },
    },
  };
</script>

<style scoped>

</style>
