<template>
    <jinya-modal @close="$emit('close')" title="support.bug_dialog.title" v-if="show" :is-fullscreen="true">
        <jinya-message slot="message" :message="message" :state="state">
            <jinya-message-action-bar v-if="state === 'error'">
                <jinya-button label="support.bug_dialog.send_mail" href="mailto:developers@jinya.de"/>
            </jinya-message-action-bar>
        </jinya-message>
        <jinya-input :required="true" label="support.bug_dialog.form.title" v-model="title"/>
        <jinya-textarea :required="true" label="support.bug_dialog.form.details" v-model="details"/>
        <jinya-textarea :required="true" label="support.bug_dialog.form.reproduce" v-model="reproduce"/>
        <jinya-choice :required="true" label="support.bug_dialog.form.severity" :choices="severityLevels"
                      @selected="severity = $event" :selected="severity"/>
        <jinya-modal-button slot="buttons-left" :closes-modal="true" label="support.bug_dialog.form.cancel"
                            :is-secondary="true"/>
        <jinya-modal-button slot="buttons-right" label="support.bug_dialog.form.submit" :is-success="true"
                            @click="submit"/>
    </jinya-modal>
</template>

<script>
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaTextarea from '@/framework/Markup/Form/Textarea';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import Translator from '@/framework/i18n/Translator';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaButton from '@/framework/Markup/Button';

  export default {
    name: 'jinya-bug-dialog',
    components: {
      JinyaButton,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaChoice,
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
    computed: {
      severityLevels() {
        return [
          {
            value: 1,
            text: Translator.message('support.bug_dialog.form.severity_levels.slightly_annoying'),
          },
          {
            value: 2,
            text: Translator.message('support.bug_dialog.form.severity_levels.annoying'),
          },
          {
            value: 3,
            text: Translator.message('support.bug_dialog.form.severity_levels.very_annoying'),
          },
          {
            value: 4,
            text: Translator.message('support.bug_dialog.form.severity_levels.usability_issues'),
          },
          {
            value: 5,
            text: Translator.message('support.bug_dialog.form.severity_levels.unusable'),
          },
          {
            value: 10,
            text: Translator.message('support.bug_dialog.form.severity_levels.security'),
          },
        ];
      },
    },
    data() {
      return {
        title: '',
        details: '',
        severity: {
          value: '1',
        },
        reproduce: '',
        message: Translator.message('support.bug_dialog.content'),
        state: 'primary',
      };
    },
    methods: {
      reset() {
        this.title = '';
        this.details = '';
        this.severity = {
          value: '1',
        };
        this.reproduce = '';
        this.message = Translator.message('support.bug_dialog.content');
        this.state = 'primary';
      },
      async submit() {
        const data = {
          title: this.title,
          details: this.details,
          severity: this.severity?.value,
          reproduce: this.reproduce,
        };

        try {
          this.message = Translator.message('support.bug_dialog.sending');
          this.state = 'loading';
          await JinyaRequest.post('/api/support/bug', data);
          this.$emit('close');
        } catch (e) {
          this.message = Translator.message('support.bug_dialog.error');
          this.state = 'error';
        }
      },
    },
  };
</script>
