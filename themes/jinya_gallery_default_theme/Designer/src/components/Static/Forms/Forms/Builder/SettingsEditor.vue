<template>
    <div class="jinya-form-builder__settings">
        <jinya-input label="static.forms.forms.builder.label" v-model="item.label" :required="true"/>
        <jinya-input label="static.forms.forms.builder.help_text" v-model="item.helpText"/>
        <jinya-checkbox label="static.forms.forms.builder.required" v-model="item.options.required"/>
        <jinya-textarea @change="item.options.choices = $event.split('\n')" label="static.forms.forms.builder.options"
                        :value="item.options.choices.join('\n')" v-if="item.type.endsWith('ChoiceType')"/>
        <jinya-button label="static.forms.forms.builder.close" :is-primary="true"/>
    </div>
</template>

<script>
  import JinyaInput from "@/framework/Markup/Form/Input";
  import JinyaCheckbox from "@/framework/Markup/Form/Checkbox";
  import JinyaButton from "@/framework/Markup/Button";
  import JinyaTextarea from "@/framework/Markup/Form/Textarea";

  export default {
    name: "jinya-form-builder-settings-editor",
    components: {
      JinyaTextarea,
      JinyaButton,
      JinyaCheckbox,
      JinyaInput
    },
    props: {
      item: {
        type: Object,
        required: true
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-form-builder__settings {
        display: none;
    }
</style>