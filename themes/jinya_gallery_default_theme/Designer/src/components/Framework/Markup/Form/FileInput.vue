<template>
    <div class="jinya-input">
        <label :for="`label-${id}`" class="jinya-input__label" v-jinya-message="label"></label>
        <label :id="`label-${id}`" :class="{'is--disabled': !enable}" :for="id" class="jinya-input__field">{{selectedFileName}}</label>
        <!--suppress HtmlFormInputWithoutLabel, HtmlFormInputWithoutLabel -->
        <input :disabled="!enable" :multiple="multiple" :id="id" type="file" :required="required" :accept="accept"
               style="display: none;" @change="updateValue($event)"/>
    </div>
</template>

<script>
  import Translator from "../../i18n/Translator";

  export default {
    name: "jinya-file-input",
    props: {
      required: Boolean,
      enable: {
        type: Boolean,
        default() {
          return false;
        }
      },
      label: {
        type: String,
        required: true
      },
      multiple: {
        type: Boolean,
        default() {
          return false;
        }
      },
      accept: {
        type: String,
        default() {
          return '*';
        }
      }
    },
    data() {
      return {
        id: null,
        selectedFileName: Translator.validator('framework.markup.form.file_input.no_file_selected')
      };
    },
    mounted() {
      this.id = this._uid;
    },
    methods: {
      updateValue(event) {
        if (this.multiple) {
          this.selectedFileName = [...event.target.files]
            .map(item => item.name.split('\\').pop().split('/').pop())
            .join(', ');
        } else {
          this.selectedFileName = event.target.files[0].name.split('\\').pop().split('/').pop();
        }
        this.$emit('picked', event.target.files);
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-input {
        font-size: 1rem;
        margin: 0 0 1em;

        .jinya-input__label {
            display: inline-block;
        }

        .jinya-input__field {
            padding: 0.5em;
            outline: none;
            width: 100%;
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
</style>