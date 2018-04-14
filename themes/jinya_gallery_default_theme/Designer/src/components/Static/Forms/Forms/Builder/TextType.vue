<template>
    <jinya-input type="text" :label="label" :required="required"/>
</template>

<script>
  import JinyaInput from "@/framework/Markup/Form/Input";
  import Translator from "@/framework/i18n/Translator";

  export default {
    name: "jinya-form-builder-text-type",
    components: {
      JinyaInput
    },
    props: {
      label: {
        type: String,
        default() {
          return Translator.message('static.forms.forms.builder.default_label');
        }
      },
      required: {
        type: Boolean,
        default() {
          return false;
        }
      }
    }
  }
</script>

<style scoped>

</style>