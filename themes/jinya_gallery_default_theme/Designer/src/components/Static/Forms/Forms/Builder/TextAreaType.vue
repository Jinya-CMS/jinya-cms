<template>
    <jinya-textarea :label="label" :required="required"/>
</template>

<script>
  import Translator from "@/framework/i18n/Translator";
  import JinyaTextarea from "@/framework/Markup/Form/Textarea";

  export default {
    name: "jinya-form-builder-text-area-type",
    components: {
      JinyaTextarea
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