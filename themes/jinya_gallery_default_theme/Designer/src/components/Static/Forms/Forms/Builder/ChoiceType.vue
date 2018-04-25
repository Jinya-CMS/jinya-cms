<template>
    <jinya-choice :enforce-select="true" :label="label" :required="required" :choices="formattedChoices"
                  :enable="enable"/>
</template>

<script>
  import Translator from "@/framework/i18n/Translator";
  import JinyaChoice from "@/framework/Markup/Form/Choice";

  export default {
    name: "jinya-form-builder-choice-type",
    components: {
      JinyaChoice
    },
    computed: {
      formattedChoices() {
        return this.choices.map(item => {
          return {
            value: item,
            text: item
          };
        });
      }
    },
    props: {
      label: {
        type: String,
        default() {
          return Translator.message('static.forms.forms.builder.default_label');
        }
      },
      choices: {
        type: Array,
        default() {
          return [];
        }
      },
      required: {
        type: Boolean,
        default() {
          return false;
        }
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        }
      }
    }
  }
</script>

<style scoped>

</style>