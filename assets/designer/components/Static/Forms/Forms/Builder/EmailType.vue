<template>
    <jinya-input :enable="enable" :label="label" :required="required" type="email"/>
</template>

<script>
  import JinyaInput from '@/framework/Markup/Form/Input';
  import Translator from '@/framework/i18n/Translator';

  export default {
    name: 'jinya-form-builder-email-type',
    components: {
      JinyaInput,
    },
    props: {
      label: {
        type: String,
        default() {
          return Translator.message('static.forms.forms.builder.default_label');
        },
      },
      required: {
        type: Boolean,
        default() {
          return false;
        },
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
    },
  };
</script>

<style scoped>

</style>
