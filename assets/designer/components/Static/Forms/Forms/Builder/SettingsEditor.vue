<template>
  <jinya-form @submit="$emit('done')" class="jinya-form-builder__settings">
    <jinya-input :required="true" :validation-message="'static.forms.forms.builder.settings.label.empty'|jvalidator"
                 label="static.forms.forms.builder.settings.label"
                 v-model="item.label"/>
    <jinya-input label="static.forms.forms.builder.settings.help_text" v-model="item.helpText"/>
    <jinya-checkbox class="jinya-form-builder__settings-checkbox" label="static.forms.forms.builder.settings.required"
                    v-model="item.options.required"/>
    <jinya-textarea :required="true"
                    :validation-message="'static.forms.forms.builder.settings.options.empty'|jvalidator"
                    :value="item.options.choices.join('\n')" @change="item.options.choices = $event.split('\n')"
                    label="static.forms.forms.builder.settings.options"
                    v-if="item.type.endsWith('ChoiceType')"/>
    <jinya-button :is-primary="true" label="static.forms.forms.builder.settings.save" slot="buttons" type="submit"/>
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
