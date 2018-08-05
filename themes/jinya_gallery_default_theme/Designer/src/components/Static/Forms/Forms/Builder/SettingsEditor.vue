<template>
  <jinya-form class="jinya-form-builder__settings" @submit="$emit('done')">
    <jinya-input label="static.forms.forms.builder.settings.label" v-model="item.label" :required="true"
                 :validation-message="'static.forms.forms.builder.settings.label.empty'|jvalidator"/>
    <jinya-input label="static.forms.forms.builder.settings.help_text" v-model="item.helpText"/>
    <jinya-checkbox label="static.forms.forms.builder.settings.required" v-model="item.options.required"
                    class="jinya-form-builder__settings-checkbox"/>
    <jinya-textarea @change="item.options.choices = $event.split('\n')" v-if="item.type.endsWith('ChoiceType')"
                    label="static.forms.forms.builder.settings.options" :value="item.options.choices.join('\n')"
                    :required="true"
                    :validation-message="'static.forms.forms.builder.settings.options.empty'|jvalidator"/>
    <jinya-button type="submit" slot="buttons" label="static.forms.forms.builder.settings.save" :is-primary="true"/>
  </jinya-form>
</template>

<script>
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaCheckbox from '@/framework/Markup/Form/Checkbox';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaTextarea from '@/framework/Markup/Form/Textarea';
  import JinyaForm from '@/framework/Markup/Form/Form';

  export default {
    name: 'jinya-form-builder-settings-editor',
    components: {
      JinyaForm,
      JinyaTextarea,
      JinyaButton,
      JinyaCheckbox,
      JinyaInput,
    },
    props: {
      item: {
        type: Object,
        required: true,
      },
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-form-builder__settings {
    padding-bottom: 1rem;
  }

  .jinya-form-builder__settings-checkbox {
    width: 100%;
    display: block;
    margin-bottom: 1rem;
  }
</style>
