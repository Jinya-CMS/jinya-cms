<template>
    <div class="jinya-input">
        <label :for="`label-${id}`" class="jinya-input__label">{{label|jmessage}}</label>
        <div class="jinya-input__wrapper">
            <label :class="{'is--disabled': !enable}" :for="id" :id="`label-${id}`" class="jinya-input__field">
                {{selectedFileName}}
            </label>
            <jinya-button :is-disabled="!enable" :is-inverse="true" :is-primary="true"
                          @click="reset" class="jinya-button--reset" label="framework.markup.form.file_input.reset"
                          type="button"/>
        </div>
        <input :accept="accept" :disabled="!enable" :id="id" :multiple="multiple" :required="required"
               @change="updateValue($event)"
               style="display: none;" type="file"/>
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
        if (this.fileName) {
          return this.fileName;
        }

        if (this.hasValue) {
          return Translator.message('framework.markup.form.file_input.already_set');
        }

        return Translator.validator('framework.markup.form.file_input.no_file_selected');
      },
    },
    data() {
      return {
        id: null,
        fileName: null,
      };
    },
    mounted() {
      // eslint-disable-next-line no-underscore-dangle
      this.id = this._uid;
    },
    methods: {
      reset() {
        this.fileName = Translator.validator('framework.markup.form.file_input.no_file_selected');
        this.$emit('picked', [null]);
      },
      updateValue($event) {
        if (this.multiple) {
          this.fileName = [...$event.target.files]
            .map(item => item.name.split('\\').pop().split('/').pop())
            .join(', ');
        } else {
          this.fileName = $event.target.files[0].name.split('\\').pop().split('/').pop();
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

<style lang="scss" scoped>
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
