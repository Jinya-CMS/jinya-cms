<template>
    <jinya-modal :is-fullscreen="true" @close="$emit('close')" title="support.like_dialog.title" v-if="show">
        <jinya-message :message="message" :state="state" slot="message"/>
        <jinya-textarea :required="true" label="support.like_dialog.form.details" v-model="details"/>
        <jinya-modal-button :closes-modal="true" :is-secondary="true" label="support.like_dialog.form.cancel"
                            slot="buttons-left"/>
        <jinya-modal-button :is-success="true" @click="submit" label="support.like_dialog.form.submit"
                            slot="buttons-right"/>
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
