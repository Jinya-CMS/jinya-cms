<template>
    <jinya-editor>
        <jinya-message :message="message" :state="state" v-if="state"/>
        <jinya-form :enable="enable" @back="$emit('back')" @submit="save"
                    cancel-label="static.forms.forms.form_form.back" class="jinya-form-form__form"
                    save-label="static.forms.forms.form_form.save">
            <jinya-input :enable="enable" :required="true"
                         :validation-message="'static.forms.forms.form_form.title.empty'|jvalidator"
                         label="static.forms.forms.form_form.title"
                         v-model="form.title"/>
            <jinya-input :enable="enable" :required="true" :validation-message="emailValidationMessage|jvalidator"
                         label="static.forms.forms.form_form.email" type="email"
                         v-model="form.toAddress"/>
            <label>{{'static.forms.forms.form_form.description'|jmessage}}</label>
            <jinya-tiny-mce :aria-label="'static.forms.forms.form_form.description'|jmessage"
                            :content="form.description"
                            v-model="form.description"/>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaTinyMce from '@/framework/Markup/Form/TinyMce';
  import Routes from '@/router/Routes';
  import JinyaMessage from '@/framework/Markup/Validation/Message';

  export default {
    name: 'jinya-form-form',
    components: {
      JinyaMessage,
      JinyaTinyMce,
      JinyaInput,
      JinyaEditor,
      JinyaForm,
    },
    props: {
      message: {
        type: String,
        default() {
          return '';
        },
      },
      state: {
        type: String,
        default() {
          return '';
        },
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
      form: {
        type: Object,
        default() {
          return {
            title: '',
            toAddress: '',
            description: '',
          };
        },
      },
    },
    data() {
      return {
        emailTypeMismatch: false,
      };
    },
    computed: {
      emailValidationMessage() {
        if (this.emailTypeMismatch) {
          return 'static.forms.forms.form_form.email.invalid';
        }

        return 'static.forms.forms.form_form.email.empty';
      },
    },
    methods: {
      back() {
        this.$router.push(Routes.Static.Forms.Forms.Overview);
      },
      save() {
        const form = {
          title: this.form.title,
          toAddress: this.form.toAddress,
          description: this.form.description,
        };

        this.$emit('save', form);
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-form-form__form {
        padding-bottom: 2rem;
    }
</style>
