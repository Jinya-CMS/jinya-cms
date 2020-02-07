<template>
    <jinya-input :enable="enable" :label="label" :value="value" @input="changed" v-if="type === 'string'"/>
    <div class="jinya-field__group" v-else-if="type === 'file'">
        <jinya-file-input :enable="enable" :has-value="hasValue" :label="label" @picked="changed"
                          class="jinya-field__input"/>
    </div>
    <jinya-checkbox :enable="enable" :label="label" :value="!!value" @input="changed" v-else-if="type === 'boolean'"/>
</template>

<script>
  import JinyaCheckbox from '@/framework/Markup/Form/Checkbox';
  import JinyaFileInput from '@/framework/Markup/Form/FileInput';
  import JinyaInput from '@/framework/Markup/Form/Input';

  export default {
    name: 'jinya-theme-configuration-field',
    components: {
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
        validator(input) {
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

<style lang="scss" scoped>
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
