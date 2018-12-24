<template>
  <jinya-input :enable="enable" :label="label" :value="value" @input="changed" v-if="type === 'string'"/>
  <div v-else-if="type === 'file'" class="jinya-field__group">
    <jinya-file-input @picked="changed" :label="label" :enable="enable" class="jinya-field__input"
                      :has-value="hasValue"/>
  </div>
  <jinya-checkbox @input="changed" :label="label" :enable="enable" v-else-if="type === 'boolean'" :value="!!value"/>
</template>

<script>
  import JinyaCheckbox from '@/framework/Markup/Form/Checkbox';
  import JinyaFileInput from '@/framework/Markup/Form/FileInput';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaButton from '@/framework/Markup/Button';

  export default {
    name: 'jinya-theme-configuration-field',
    components: {
      JinyaButton,
      JinyaInput,
      JinyaFileInput,
      JinyaCheckbox,
    },
    props: {
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
      value: {
        required: true,
      },
      type: {
        type: String,
        required: true,
        validate(input) {
          return ['string', 'file', 'boolean'].includes(input);
        },
      },
      label: {
        type: String,
        required: true,
      },
      name: {
        type: String,
        required: true,
      },
    },
    data() {
      return {
        hasValue: !!this.value,
      };
    },
    methods: {
      changed($event) {
        const payload = {
          name: this.name,
          type: this.type,
        };

        switch (this.type) {
          case 'file':
            payload.value = $event.item(0);
            payload.label = this.label;
            break;
          case 'string':
            payload.value = $event;
            break;
          case 'boolean':
            payload.value = !!$event;
            break;
          default:
            break;
        }
        this.$emit('changed', payload);
      },
    },
  };
</script>

<style scoped lang="scss">
  .jinya-field__group {
    margin-bottom: 1em;

    .jinya-field__input {
      margin-bottom: 0;
    }

    .jinya-field__button {
      width: 100%;
      padding: 0;
      vertical-align: bottom;
    }
  }
</style>
