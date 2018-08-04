<template>
  <div class="jinya-input">
    <label :for="`label-${id}`" class="jinya-input__label">{{label|jmessage}}</label>
    <div class="jinya-input__wrapper">
      <label :id="`label-${id}`" :class="{'is--disabled': !enable}" :for="id" class="jinya-input__field">
        {{selectedFileName}}
      </label>
      <jinya-button label="framework.markup.form.file_input.reset" class="jinya-button--reset" :is-primary="true"
                    :is-inverse="true" type="button" :is-disabled="!enable" @click="reset"/>
    </div>
    <input :disabled="!enable" :multiple="multiple" :id="id" type="file" :required="required" :accept="accept"
           style="display: none;" @change="updateValue($event)"/>
  </div>
</template>

<script>
  import Translator from '@/framework/i18n/Translator';
  import JinyaButton from '@/framework/Markup/Button';

  export default {
    name: 'jinya-file-input',
    components: { JinyaButton },
    props: {
      required: Boolean,
      enable: {
        type: Boolean,
        default() {
          return false;
        },
      },
      label: {
        type: String,
        required: true,
      },
      multiple: {
        type: Boolean,
        default() {
          return false;
        },
      },
      accept: {
        type: String,
        default() {
          return '*';
        },
      },
      hasValue: {
        type: Boolean,
        default() {
          return false;
        },
      },
    },
    computed: {
      selectedFileName() {
        return this.hasValue
          ? Translator.message('framework.markup.form.file_input.already_set')
          : Translator.validator('framework.markup.form.file_input.no_file_selected');
      },
    },
    data() {
      return {
        id: null,
      };
    },
    mounted() {
      // eslint-disable-next-line no-underscore-dangle
      this.id = this._uid;
    },
    methods: {
      reset() {
        this.selectedFileName = Translator.validator('framework.markup.form.file_input.no_file_selected');
        this.$emit('picked', [null]);
      },
      updateValue($event) {
        if (this.multiple) {
          this.selectedFileName = [...$event.target.files]
            .map(item => item.name.split('\\').pop().split('/').pop())
            .join(', ');
        } else {
          this.selectedFileName = $event.target.files[0].name.split('\\').pop().split('/').pop();
        }
        this.$emit('picked', $event.target.files);
        if ($event.target.checkValidity()) {
          this.$emit('valid');
        } else {
          this.$emit('invalid', $event.target.validity);
        }
      },
    },
  };
</script>

<style scoped lang="scss">
  .jinya-input {
    font-size: 1rem;
    width: 100%;

    @include validation-message(3.4rem);

    .jinya-input__label {
      display: inline-block;
    }

    .jinya-input__wrapper {
      width: 100%;
      display: flex;

      .jinya-button--reset {
        flex: 0 0 auto;
        min-width: 5em;
        padding: 0 0.5em;
        border: none;
        position: unset;
        border-bottom: 3px solid $form-underline-color;
      }

      .jinya-input__field {
        flex: 1 1 auto;
        padding: 0.5em;
        outline: none;
        justify-self: flex-start;
        display: inline-block;
        border-style: none;
        border-bottom: solid 3px $form-underline-color;
        transition: border-bottom-width 0.3s;
        font-family: $font-family;
        font-size: 90%;
        background-color: $white;

        &.is--disabled {
          background: $gray-200;
        }
      }
    }
  }
</style>
