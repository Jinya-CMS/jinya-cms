<template>
  <jinya-editor>
    <jinya-message :message="message" :state="state" v-if="state"/>
    <jinya-form cancel-label="static.forms.forms.form_form.back" @back="$emit('back')" :enable="enable"
                save-label="static.forms.forms.form_form.save" @submit="save" class="jinya-form-form__form">
      <jinya-input label="static.forms.forms.form_form.title" :enable="enable" @change="titleChanged"
                   v-model="form.title"/>
      <jinya-input label="static.forms.forms.form_form.slug" :enable="enable" @change="slugChanged"
                   v-model="form.slug"/>
      <jinya-input label="static.forms.forms.form_form.email" :enable="enable" type="email"
                   v-model="form.toAddress"/>
      <label>{{'static.forms.forms.form_form.description'|jmessage}}</label>
      <jinya-tiny-mce :content="form.description" v-model="form.description"
                      :aria-label="'static.forms.forms.form_form.description'|jmessage"/>
    </jinya-form>
  </jinya-editor>
</template>

<script>
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaTinyMce from '@/framework/Markup/Form/TinyMce';
  import Routes from '@/router/Routes';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import slugify from 'slugify';

  export default {
    name: 'jinya-form-form',
    components: {
      JinyaMessage,
      JinyaMessageActionBar,
      JinyaButton,
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
            slug: '',
            toAddress: '',
            description: '',
          };
        },
      },
      slugifyEnabled: {
        type: Boolean,
        default() {
          return true;
        },
      },
    },
    methods: {
      back() {
        this.$router.push(Routes.Static.Forms.Forms.Overview);
      },
      titleChanged(value) {
        if (this.slugifyEnabled) {
          this.form.slug = slugify(value);
        }
      },
      slugChanged(value) {
        this.slugifyEnabled = false;
        this.form.slug = slugify(value);
      },
      save() {
        const form = {
          title: this.form.title,
          slug: this.form.slug,
          toAddress: this.form.toAddress,
          description: this.form.description,
        };

        this.$emit('save', form);
      },
    },
  };
</script>

<style scoped lang="scss">
  .jinya-form-form__form {
    padding-bottom: 2rem;
  }
</style>
