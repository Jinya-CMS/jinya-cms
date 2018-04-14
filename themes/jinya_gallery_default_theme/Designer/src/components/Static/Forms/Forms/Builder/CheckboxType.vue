<template>
    <jinya-checkbox :label="label" :required="required"/>
</template>

<script>
  import Translator from "@/framework/i18n/Translator";
  import JinyaCheckbox from "@/framework/Markup/Form/Checkbox";

  export default {
    name: "jinya-form-builder-checkbox-type",
    components: {
      JinyaCheckbox
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